# AutoClearLagg

Periodically clear selected dropped items, mobs and XP orbs on Axolotl-PM

[![CI](https://github.com/NhanAZ-Plugins/AutoClearLagg/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/AutoClearLagg/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/AutoClearLagg)](https://github.com/NhanAZ-Plugins/AutoClearLagg/releases/latest)
[![License](https://img.shields.io/github/license/NhanAZ-Plugins/AutoClearLagg)](https://github.com/NhanAZ-Plugins/AutoClearLagg/blob/master/LICENSE)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)

## Overview

This plugin targets the PocketMine-MP and Axolotl-PM API 5 ecosystem and is maintained under [NhanAZ-Plugins](https://github.com/NhanAZ-Plugins).

## Requirements and compatibility

- Axolotl-PM API 5.0.0 or newer within the 5.x API series. PHPStan checks use pinned 5.0.0 and 5.49.1 source revisions. Compatibility with every intermediate release has not been verified.
- PHP 8.1 or newer, as required by Axolotl-PM 5.
- No other plugin is required.

## Installation

Download the PHAR from [GitHub Releases](https://github.com/NhanAZ-Plugins/AutoClearLagg/releases), place it in the server's `plugins` directory and restart the server. Review the [default configuration](resources/config.yml) before using this plugin on a production world.

Cleanup removes entities from the loaded worlds. It does not make backups of dropped items or mobs. Stop the server and back up your world data before changing cleanup policy.

## Configuration

AutoClearLagg stores `config.yml` in its plugin data folder. The bundled configuration uses the values below. Missing cleanup flags default to `false`, and a missing exception list is empty. Invalid values produce a warning and prevent cleanup from starting until the configuration is corrected and the server is restarted.

| Setting | Default | Effect |
|---|---|---|
| `seconds` | `300` | Positive interval between cleanup passes, in seconds. |
| `clear.items` | `true` | Remove dropped item entities. |
| `clear.mobs` | `true` | Remove nonplayer living entities unless their names are exempt. |
| `clear.xp-orbs` | `true` | Remove experience orbs. |
| `clear.exempt` | `Zombie`, `Pig` | Case insensitive entity names to keep when mob cleanup is enabled. |
| `times` | `60, 30, 15, 10, 5, 4, 3, 2, 1` | Positive countdown seconds when a warning may be broadcast. |
| `messages.time-left` | Countdown text | Warning with the `{SECONDS}` placeholder. |
| `messages.entities-cleared` | Result text | Message with the `{COUNT}` placeholder. |

Set either message to an empty string to suppress it. Entity names are matched against Axolotl-PM's `getName()` value, not a translated display name. Players and human based NPCs are excluded from mob cleanup.

## Development and validation

The [build workflow](.github/workflows/build.yml) runs PHPStan at maximum level and uses [DevTools](https://github.com/NhanAZ/DevTools) to build and inspect the PHAR. Its pinned source and tool revisions, along with exact test results, are recorded in [GitHub Actions](https://github.com/NhanAZ-Plugins/AutoClearLagg/actions).

## Support and credits

Report issues in this repository or join [NhanAZ Discord](https://discord.gg/j2X83ujT6c). AutoClearLagg originated with Twisted and received contributions from other developers listed in Git history. This fork retains the original GPL-3.0 license and notices.

## License

AutoClearLagg is distributed under the [GNU General Public License version 3](LICENSE).
