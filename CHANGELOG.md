# Changelog

## 4.0.3

- Replace the removed `Creature` class check with Axolotl-PM's `Living` type while excluding `Human` entities.
- Reject invalid timer, cleanup, exception, message and countdown values before starting the scheduler without aborting server startup.
- Document the cleanup policy and add a pinned DevTools build workflow.
