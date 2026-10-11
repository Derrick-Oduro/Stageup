# StageUp Project Plan

## Current project baseline

This workspace is already a Laravel application with a working foundation:

- Laravel 13.x with PHP 8.3 support
- Laravel Breeze starter authentication
- Spatie permission package already installed
- SQLite configured by default for local development
- Vite + Tailwind for the frontend build pipeline
- Existing `Tournament` and `User` models plus a public tournament flow
- Initial tests covering public tournament pages and participant engagement

The project is no longer a blank scaffold; it is a partially implemented tournament platform with the earliest foundations already in place.

## Product intent

StageUp is an eFootball community and tournament platform combining:

- public tournament listings and detail pages
- member registration and participation
- admin-managed tournament lifecycle
- tournament scoring, verification, and standings
- notifications and future community / chat layers

## Architecture decisions

### Backend

- Laravel as the primary application framework
- Eloquent models and migrations for data persistence
- service classes for tournament-domain operations
- validation via Form Requests, where the business rules warrant it
- authorization through policies, gates, and role checks

### Frontend

- Blade views for the server-rendered UI
- Tailwind CSS for styling
- Vite for asset bundling
- reusable Blade components for navigation and cards

### Data model

- relational schema with normalized tables, foreign keys, and indexes
- explicit lifecycle state for tournaments and fixtures
- strict separation between submitted scores, verified official results, and derived standings

## Phase roadmap

### Phase 1 — Foundation and governance

Status: in progress / active baseline

Completed or present:

- Laravel app initialization and environment configuration
- authentication and session setup
- role and permission package integration
- tournament model + migration foundation
- public tournament index and detail views
- baseline feature tests for tournament visibility and participation

Next tasks:

- lock the role/permission matrix for guest/member/moderator/organizer/admin
- finalize profile and audit models
- add explicit admin pages and route protection
- create robust tournament state transitions and config schema
- standardize docs and environment configuration

### Phase 2 — Tournament MVP

- tournament CRUD and lifecycle management
- registration validation
- round-robin fixture generation
- independent score submission by both players
- comparison and dispute detection
- administrator verification
- official result approval and standings recalculation

### Phase 3 — Email, reminders, and notifications

- queued mail
- user verification flow
- tournament notifications
- reminder scheduling
- in-app notification center

### Phase 4 — Tournament format expansion

- single-elimination brackets
- group stage + knockout
- team and roster handling
- custom stacked team tournaments
- configurable tie-break and progression rules

### Phase 5 — Community features

- forum categories, topics, posts, moderation
- report handling
- announcements
- discussion permissions

### Phase 6 — Realtime chat

- chat rooms and membership
- message history and pagination
- Reverb/broadcasting integration
- moderation and rate limits

### Phase 7 — Production readiness

- final authorization review
- queue and scheduler verification
- deployment and monitoring setup
- final security and performance pass

## Key product and engineering principles

1. Prefer explicit domain logic over manually duplicated checks in controllers.
2. Never trust UI-only authorization; enforce every privileged action on the server.
3. Keep official results authoritative and separate from user-submitted data.
4. Recalculate standings and bracket progression from verified official results only.
5. Use queue-based mail and idempotent reminders to avoid duplicate notifications.
6. Add tests for authorization, data integrity, and business rules, not only page rendering.

## Open decisions to confirm

- whether tournament participants are individual users only or also team entities
- how custom stacked team tournaments should be stored in the schema
- whether admin and organizer roles must be separate permission sets or one shared role hierarchy
- whether chat will use Reverb in v1 or a simpler in-app-only model for the first stable release

## Implementation checklist

- [x] Environment inspection complete
- [x] Baseline Laravel app confirmed
- [x] Core docs initialized
- [x] profile and user identity foundation added
- [x] role and permission framework validated around the existing Spatie setup
- [x] add audit log foundation
- [x] implement tournament state and config domain
- [ ] build a secure result workflow
- [ ] add notification queue and reminder jobs
- [ ] implement broader tournament formats and team support
- [ ] add forum and chat features after core tournament flow is stable
