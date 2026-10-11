# Security plan

## Current security baseline

The project already uses:

- Laravel auth
- session-based authentication
- password hashing via Laravel defaults
- Spatie permission for role-based access control

This is the right baseline for a production-minded application, but it still needs explicit hardening and policy enforcement as the feature set grows.

## Required security principles

### Authorization

- use policies and middleware for all privileged operations
- never hide buttons as the only control mechanism
- enforce access checks server-side for admin dashboards, moderator actions, and official result approvals

### Input validation and CSRF

- validate all incoming requests server-side
- protect state-changing routes with CSRF middleware
- rate-limit actions that are abuse-prone, such as score submission, chat posting, and auth attempts

### Auditability

- log privileged updates in a dedicated audit ledger
- record admin ID, timestamp, and reason for result overrides or moderation actions
- preserve historical state for official result corrections

### Result integrity

- never allow a public form field to set an official result directly
- keep user-reported data separate from official results
- require server-side review for changes to standing or advancement state

### Notifications and account security

- verify email before enabling high-trust account actions
- queue mail rather than sending synchronously on every request
- use secure, expiring tokens for sensitive actions where applicable
- avoid exposing private email addresses or recipient lists

### File and upload handling

- validate upload type, size, and storage policy
- restrict file access to authorized users
- never expose raw file paths or private storage locations

## Important rules for the first release

- a member must not be able to promote themselves to admin by manipulating request data
- users must not submit results for another person's match or alter opposing scores
- moderators cannot approve official results unless explicitly granted that permission
- score approval and result correction must be transactional

## Testing checklist

The security model should be validated with tests covering:

- guest access denial
- member access denial for admin routes
- organizer privilege boundaries
- disallowed self-approval and self-correction
- duplicate and concurrent submission handling
- result override audit metadata

## Recommended future hardening

- explicit `Policy` classes for tournament, fixture, forum, and moderation actions
- route-specific access middleware for organizers and admins
- secure admin area separation and session protection
- HTTPS enforcement in production
- centralized logging and monitoring for suspicious activity
