# TT Dental Lab Attendance - Presentation Upgrade

This package upgrades the existing Laravel project without rebuilding it from scratch.

## Included
- Professional shared left sidebar/topbar layout across authenticated management pages
- Presentation-ready dashboard with attendance KPIs and recent activity
- Employee management UI with search, status and biometric indicators
- Attendance report filters and CSV export action
- Absence management UI
- Settings page so the sidebar route is valid
- Secure OTP password reset flow: hashed OTP, 15-minute expiry, maximum 5 attempts, one-time verification, password hashing, session invalidation after reset

## Database safety
No destructive database reset is included. Do not run `migrate:fresh`, `db:wipe`, or `truncate`.

## Important
The source `.env` file is intentionally excluded from this package. Keep your existing `.env` in your local project.

For real email OTP delivery, configure SMTP in the existing `.env`. The supplied project currently uses Laravel's `log` mailer, which is suitable for local development but does not send to Gmail.
