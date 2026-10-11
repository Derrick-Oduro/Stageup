# Tournament engine design

## Summary

The tournament engine should be a domain layer with clear responsibilities and controlled lifecycle transitions. The current app already has a minimalist `Tournament` model and public registration flow, but the full engine should be added with explicit services and configuration patterns instead of hard-coded logic embedded in controllers.

## Core responsibilities

The tournament engine should separate responsibilities into services such as:

- `TournamentService`
- `TournamentRegistrationService`
- `TournamentFormatResolver`
- `FixtureGenerationService`
- `FixtureSchedulingService`
- `ScoreSubmissionService`
- `ResultVerificationService`
- `StandingsCalculationService`
- `TournamentAdvancementService`
- `TournamentNotificationService`

This is the correct shape for a platform expected to support multiple formats and verification rules.

## Lifecycle states

Recommended tournament states:

- `draft`
- `registration_open`
- `registration_closed`
- `scheduled`
- `in_progress`
- `completed`
- `cancelled`

Allowed transitions should be defined in a state machine rather than scattered conditions in controllers.

Example rules:

- `draft` -> `registration_open`
- `registration_open` -> `registration_closed`
- `registration_closed` -> `scheduled`
- `scheduled` -> `in_progress`
- `in_progress` -> `completed`
- `registration_open` / `scheduled` / `in_progress` -> `cancelled`

## Supported formats

### 1. League / round robin

Needs:

- participant registration
- fixture generation with round structure
- configurable home/away or repeated matches
- scoring rules for win/draw/loss
- standings based on verified results only
- tie-breakers with per-tournament config

### 2. Single-elimination knockout

Needs:

- bracket generation with seeding support
- byes handling
- advancement after official result approval
- optional third-place match
- winner representation and final progression rules

### 3. Group stage followed by knockout

Needs:

- multiple groups
- qualification thresholds
- separate group standings
- seeded progression into later knockout rounds

### 4. Custom stacked team tournament

This is the highest-risk format and should be treated as a dedicated format strategy rather than a one-off condition.

Key configuration inputs:

- players per team
- required positions and optional slots
- per-match counts
- pairing generation strategy
- aggregate scoring model
- walkover and incomplete fixture rules
- advancement logic

Important design rule: the overall team tie must remain separate from individual fixture scoring. A team can win a tie by aggregate match results, not simply by total goals or total wins.

## Fixture lifecycle states

Suggested fixture states:

- `scheduled`
- `awaiting_players`
- `awaiting_scores`
- `awaiting_verification`
- `disputed`
- `verified`
- `postponed`
- `cancelled`
- `walkover`

Rules:

- a submitted score does not become official by itself
- both participating users must submit valid scores before auto-comparison
- mismatched submissions create a dispute record
- only approved results can move the tournament forward

## Score reporting workflow

1. Fixture is created and scheduled.
2. Players submit their own reported score.
3. Matching reports are compared.
4. If they match, fixture moves to `awaiting_verification`.
5. If they conflict, fixture moves to `disputed`.
6. Admin or authorized organizer reviews the evidence and approves or overrides.
7. Official result is stored separately and triggers standings recalculation.

## Integrity safeguards

- separate `score_submissions` from `official_results`
- require transaction-based approvals for critical updates
- reject duplicate or concurrent result approvals
- preserve the original submissions even after corrections
- prevent players from altering an opponent's score
- store all result overrides in an append-only history table

## Standings and progression rules

Standings calculations should come from one authoritative service per format:

- league standings service
- group standings service
- knockout advancement service
- custom team aggregate service

No page-level ad hoc logic should calculate winner or qualification status.

## Operational principles

- no fixture regeneration once a result exists without explicit admin confirmation
- qualification and standings are derived from official results, never from unverified submissions
- configuration must be validated before format activation
- unsupported configuration should fail loudly and clearly instead of silently falling back

## Extension plan

Add new format strategies by implementing a common interface or contract:

- `TournamentFormatStrategy`
- `generateFixtures()`
- `validateConfiguration()`
- `calculateStandings()`
- `determineProgression()`

This keeps a new format isolated without creating a large conditional controller.
