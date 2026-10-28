# Frozen: no new migrations here

Since 2026-10-20 the database schema is owned by **domingoasdez-admin**
(`C:\Users\Tiago\Desktop\repos\domingoasdez-admin`, `drizzle/migrations`). Its baseline is the
schema these migrations built, up to `2026_09_25_114800_add_team_to_player_update_requests_table`.

- Do not add migrations to this folder.
- Do not run `php artisan migrate`, `migrate:fresh`, `migrate:rollback` or `migrate:refresh`
  against a shared database: the admin's migrations are not recorded in the `migrations` table, and
  the `down()` methods know nothing about them.
- The cron jobs and queues keep running on this site for now. Admin migrations stay compatible
  with them (new columns are nullable or have defaults); when a model needs a new column, add it
  to the model here, not a migration.
