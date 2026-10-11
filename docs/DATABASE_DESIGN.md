# Database design

## Baseline status

The current codebase already includes:

- `users`
- `tournaments`
- `tournament_followers`
- `tournament_participants`
- permission tables from Spatie

This is a strong base for the next phases and should be extended rather than replaced.

## Core design goals

- keep public profile data separate from private account data
- normalize tournament, roster, fixture, and result records
- preserve a strict audit trail for privileged actions
- support multiple tournament formats without compromising consistency
- enforce unique and valid relationships at the database level where practical

## Proposed canonical entities

### Accounts and identity

#### `users`

Primary account record.

Fields should include:

- id
- name
- email
- email_verified_at
- password
- remember_token
- timestamps

Notes:

- use Laravel's built-in authentication and hashing
- keep user profile metadata in separate profile models/table if this grows beyond a lightweight profile

#### `profiles`

Optional profile extension table for public profile data.

Suggested fields:

- user_id
- username
- avatar_url
- bio
- timezone
- country
- socials
- created_at / updated_at

#### `roles` and `permissions`

Use the Spatie permission tables already installed by the project.

### Tournament domain

#### `tournaments`

Required data:

- id
- organizer_id
- name
- slug
- game
- format
- status
- registration_deadline
- starts_at
- max_participants
- participants_count
- followers_count
- is_public
- rules
- timestamps

Indexes:

- unique slug
- public + status lookup
- game + status lookup
- organizer lookup

#### `tournament_entries`

This is the canonical participant registration record for tournament ownership or entry.

Suggested fields:

- id
- tournament_id
- entrant_type (user|team)
- entrant_id
- status
- created_at / updated_at

Notes:

- use either a polymorphic approach or a simplified `user_id`/`team_id` split depending on complexity
- enforce uniqueness per tournament + entrant combination
- keep this as the base registration entity, not just a pivot table

#### `teams`

Suggested fields:

- id
- name
- slug
- captain_user_id
- tournament_id (optional if roster is tournament-scoped)
- created_at / updated_at

#### `team_members`

- id
- team_id
- user_id
- role (captain, member, substitute)
- joined_at
- timestamps

#### `tournament_stages`

- id
- tournament_id
- stage_type (group|knockout|league)
- name
- order
- status
- timestamps

#### `tournament_groups`

- id
- stage_id
- name
- sort_order
- timestamps

#### `tournament_group_entries`

- id
- group_id
- tournament_entry_id
- standings_position (nullable)
- timestamps

#### `tournament_format_configs`

Store format configuration in structured JSON or normalized config columns.

Suggested fields:

- id
- tournament_id
- format_name
- config (json)
- created_at / updated_at

This should be used for:

- round robin point rules
- group sizes
- seeding
- match counts
- qualification thresholds
- custom team tournament rules

#### `fixtures`

Core fixture row:

- id
- tournament_id
- stage_id
- group_id (nullable)
- round_name
- scheduled_at
- reporting_deadline
- status
- notes
- created_at / updated_at

Additional derived relationships:

- `fixture_participants` for the participants or teams involved in a specific fixture
- `score_submissions` for each side's reported numeric score
- `official_results` for the authoritative result once approved

#### `fixture_participants`

- id
- fixture_id
- participant_type (user|team)
- participant_id
- side (home|away|team_a|team_b)
- seed
- is_substitute
- timestamps

#### `score_submissions`

- id
- fixture_id
- submitted_by_user_id
- participant_id
- score_for_participant
- score_against_participant
- notes
- evidence_url (nullable)
- status
- created_at / updated_at

#### `official_results`

- id
- fixture_id
- winner_participant_id (nullable)
- loser_participant_id (nullable)
- score_for
- score_against
- verified_by_user_id
- verification_reason
- approved_at
- timestamps

#### `result_history`

Append-only tracking for result changes.

- id
- fixture_id
- previous_result_json
- new_result_json
- changed_by_user_id
- reason
- timestamps

### Community and moderation

#### `forum_categories`, `forum_topics`, `forum_posts`

Core discussion model with soft deletes and moderation support.

#### `chat_rooms`, `chat_room_members`, `chat_messages`

General and tournament-specific chat rooms.

#### `announcements`

- id
- title
- body
- created_by
- published_at
- audience_scope

#### `reports`

- id
- reportable_type
- reportable_id
- reported_by_user_id
- reason
- status
- resolved_by_user_id
- timestamps

#### `moderation_actions`

- id
- moderator_id
- target_type
- target_id
- action_type
- notes
- timestamps

### Notifications and auditing

#### `notifications`

Laravel-provided notifications table should be used for in-app notification state.

#### `audit_logs`

- id
- actor_user_id
- subject_type
- subject_id
- action
- metadata
- ip_address
- created_at

## Integrity principles

- enforce uniqueness where a user cannot re-register in the same tournament
- restrict invalid participant relationships in the application layer and DB constraints where possible
- use integer score fields that are nonnegative
- use transactions around result approval and progression changes
- do not cascade-delete official historical records
- record all privileged changes in an append-only audit trail

## Future migration note

The current schema is a good MVP foundation, but it is not yet sufficient for advanced custom stacked team tournaments. That format should be introduced through a dedicated configuration and fixture model rather than by overloading the base `tournaments` table.
