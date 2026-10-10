# TourneyHub

> A web-based tournament hosting platform. Create, manage, and follow esports tournaments — from casual community brackets to organised competitive events.

---

## Table of Contents

- [Overview](#overview)
- [User Roles](#user-roles)
- [Features](#features)
- [User Flows](#user-flows)
- [Tech Stack](#tech-stack)
- [Roadmap](#roadmap)
- [Open Questions](#open-questions)

---

## Overview

TourneyHub lets anyone create and host a tournament in minutes. Other users can follow a tournament as spectators or register as participants. Bracket generation, match scheduling, and results tracking are handled by the platform.

The project launches with a clean core (v1.0) modelled after tools like EasyTournament, then builds on top of that foundation with more formats and social features in later releases.

**Core principles:**

- Any registered user can register as an organiser and host
- Following a tournament and participating in it are separate, independent actions — follower count and participant count will not always be equal, and that is by design
- The platform is built to scale — new features slot in without breaking what already works

---

## User Roles

| Role            | Description                     | Key Permissions                                                             |
| --------------- | ------------------------------- | --------------------------------------------------------------------------- |
| Guest           | Unauthenticated visitor         | Browse public tournaments, view brackets                                    |
| Registered User | Logged-in player or viewer      | Follow tournaments, register as participant, receive notifications          |
| Organizer       | Creates and manages tournaments | Full tournament CRUD, participant management, bracket control, result entry |
| Admin           | Platform-level control          | User management, content moderation, platform settings                      |

---

## Features

### Authentication & Accounts

- User registration (email + password) with email verification
- Login with session management
- User profile — display name, avatar, bio, game tags, tournament history
- OAuth login via Google / Discord _(v1.1)_
- Two-factor authentication for organizer and admin accounts _(v1.1)_

### Tournament Management

- Create a tournament — name, game, format, dates, registration deadline, max participants, prize info, rules, public/private toggle
- Bracket auto-generation on participant lock, with seeding support
- Match scheduling with automatic participant notifications
- Real-time bracket updates as organizer enters results
- Open or invite-only participant registration
- Follow a tournament without joining as a competitor
- Organizer dashboard — participant list, bracket status, pending results, notification log
- Public shareable tournament page with bracket view and match schedule

### Bracket Formats

- Single elimination _(v1.0)_
- Double elimination _(v1.1)_
- Round robin _(v1.1)_
- Swiss format _(v2.0)_
- Group stage + knockout _(v2.0)_

### Social & Notifications

- In-app notifications — match reminders, result updates, tournament announcements
- Email notifications for key events
- Per-tournament comment / discussion board _(v1.1)_
- Discord integration — push tournament events to a connected server _(v1.1)_
- Player follow / friends and activity feed _(v2.0)_

### Admin & Moderation

- Admin dashboard — manage users, view all tournaments, resolve disputes
- Content reporting — flag inappropriate tournament names, descriptions, or comments
- Organizer verification badge for trusted community hosts _(v1.1)_

### Future Expansions _(v2.0)_

- Live match stream embed (Twitch / YouTube) on the tournament page
- Prize pool and entry fee collection via payment gateway
- Team / clan support — team brackets and team profiles
- Organizer analytics — registrations over time, match completion rate
- Mobile app (iOS and Android)

---

## User Flows

### Creating a Tournament

1. Organizer clicks **Create Tournament**
2. Fills in name, game, format, start date, registration deadline, max participants, prize description, and rules
3. Sets visibility — **Public** (anyone can see and join) or **Private** (invite-only via link)
4. Publishes — tournament is now live on the platform

### Following a Tournament

1. Any logged-in user visits a tournament page
2. Clicks **Follow**
3. Receives notifications for bracket release, match results, and final standings
4. Follower count is shown on the tournament page, separate from participant count

### Joining as a Participant

1. Logged-in user visits a public tournament before the registration deadline
2. Clicks **Register / Join**
3. For invite-only tournaments, they must use the organizer-provided link
4. Once registered, they appear in the participant list and are seeded into the bracket
5. A user can follow **and** participate — these are independent actions

### Running the Bracket

1. Registration closes (deadline hit or capacity reached)
2. Organizer locks participants and clicks **Generate Bracket**
3. Organizer sets match times — participants are notified automatically
4. After each match, organizer enters the score — bracket advances in real time
5. Final match produces a winner; standings are published automatically

---

## Tech Stack

> Draft suggestions — final stack should be decided based on team expertise.

| Layer          | Approach                                                                                 |
| -------------- | ---------------------------------------------------------------------------------------- |
| Frontend       | React (Next.js for SSR + SEO), Tailwind CSS                                              |
| Backend / API  | Node.js + Express or NestJS. REST API to start; WebSockets for live bracket updates      |
| Database       | PostgreSQL for relational data. Redis for session caching and real-time pub/sub          |
| Authentication | JWT-based auth. Passport.js for OAuth (v1.1)                                             |
| Hosting        | Vercel (frontend) + Railway or Render (backend + DB) for v1.0. Scale to AWS/GCP for v2.0 |

---

## Roadmap

| Version | Focus                                                                                                  |
| ------- | ------------------------------------------------------------------------------------------------------ |
| v1.0    | Core platform — accounts, tournament creation, single-elim brackets, follow/participate, notifications |
| v1.1    | OAuth, double elimination, round robin, Discord integration, comment boards, organizer badge           |
| v2.0    | Swiss/group formats, teams, prize pools, live stream embeds, analytics, mobile app                     |

---

## Open Questions

- **Platform name** — TourneyHub is a placeholder. Final name TBD.
- **Monetization** — free to host? Premium organizer plans? Entry fee cut? TBD.
- **Participant cap** — free tier vs. paid tier limits need a product decision.
- **Dispute resolution** — how does a participant contest a result entered by the organizer?
- **Team support** — v1.0 targets 1v1 formats. The data model should accommodate teams even if the UI does not surface it yet.
- **Anonymous participation** — should non-logged-in users be able to join via a link? Out of scope for v1.0.

---

## Version Log

| Version | Date       | Notes         |
| ------- | ---------- | ------------- |
| 0.1     | 2026-05-31 | Initial draft |
