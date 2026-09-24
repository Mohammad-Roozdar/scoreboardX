# ScoreboardX

[![PocketMine-MP](https://img.shields.io/badge/PocketMine--MP-5.0.0+-blue.svg)](https://github.com/pmmp/PocketMine-MP)
[![Minecraft](https://img.shields.io/badge/Minecraft-Bedrock%201.21.130-green.svg)](https://www.minecraft.net/)
[![Version](https://img.shields.io/badge/version-1.0.0-orange.svg)]()
[![License](https://img.shields.io/badge/license-MIT-lightgrey.svg)]()

> A modern, lightweight, and fully configurable scoreboard plugin for PocketMine-MP 5.

ScoreboardX is a professional scoreboard plugin designed for **PocketMine-MP 5.39.3** and **Minecraft Bedrock Edition 1.21.130**. It features a clean codebase, dynamic placeholders, title animation, and zero flickering — everything you need in a sidebar scoreboard.

---

## ✨ Features

- 🎨 **Fully Configurable** — Edit every line, color, and title directly from `config.yml`
- 🚫 **Zero Flickering** — Smart packet handling prevents visual flicker
- 🔄 **Live Placeholders** — Supports `{player}`, `{online}`, `{max}`, `{ping}`, `{world}`, `{x}`, `{y}`, `{z}`, `{tps}`, `{time}`, `{date}`, `{line}`
- 🎬 **Animated Title** — Cycle through multiple frames for a dynamic sidebar title
- 🧹 **Clean Removal** — Removes objective packets on quit to prevent ghosts
- 🛡️ **Crash-Proof** — Safety checks everywhere to prevent server crashes
- 🌍 **Lightweight** — No dependencies required
- ⚡ **Optimized** — Minimal overhead, high performance

---

## 📦 Installation

1. Download the latest `ScoreboardX.phar` from the [Releases](https://github.com/YourRepo/ScoreboardX/releases) page.
2. Place the `.phar` file inside the `plugins/` folder of your PocketMine-MP server.
3. Restart the server.
4. Edit `plugins/ScoreboardX/config.yml` to customize your scoreboard.
5. Reload with `/scoreboard reload` or restart the server.

---

## 🛠️ Building from Source

If you want to build the plugin yourself:

1. Clone this repository:
   ```bash
   git clone https://github.com/YourRepo/ScoreboardX.git
   ```
2. Copy the `ScoreboardX` folder into your server's `plugins/` directory.
3. Use [DevTools](https://github.com/pmmp/DevTools) and run:
   ```
   /makeplugin ScoreboardX
   ```
4. The `.phar` file will be generated inside the `plugins/` folder.

---

## ⚙️ Configuration

Example `config.yml`:

```yaml
update-interval: 20
enabled-by-default: true

title:
  animated: true
  animation-speed: 20
  frames:
    - "§6§lScoreboardX"
    - "§e§lScoreboardX"

lines:
  - "§7§m----------------"
  - "§fPlayer: §b{player}"
  - "§fOnline: §a{online}§7/§a{max}"
  - "§fPing: {ping}ms"
  - "§fWorld: §e{world}"
  - "§7§m----------------"
  - "§fX: §6{x}"
  - "§fY: §6{y}"
  - "§fZ: §6{z}"
  - "§7§m----------------"
```

---

## 🎮 Commands

| Command | Description | Permission |
| :--- | :--- | :--- |
| `/scoreboard on [player]` | Enable the scoreboard | `scoreboardx.command` |
| `/scoreboard off [player]` | Disable the scoreboard | `scoreboardx.command` |
| `/scoreboard reload` | Reload the config | `scoreboardx.command` |
| `/scoreboard version` | Show plugin info | `scoreboardx.command` |

**Aliases:** `/sb`, `/score`

---

## 🔑 Permissions

| Permission | Default | Description |
| :--- | :--- | :--- |
| `scoreboardx.command` | `true` | Allows using the `/scoreboard` command |

---

## 📸 Preview

*(Add a screenshot here)*

---

## 🧩 Compatibility

| Software | Version |
| :--- | :--- |
| PocketMine-MP | 5.0.0+ |
| Minecraft Bedrock | 1.21.130 |
| PHP | 8.0+ |

---

## 🐛 Bug Reports & Suggestions

Found a bug or have an idea? Please open an [issue](https://github.com/YourRepo/ScoreboardX/issues).

---

## 📜 License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 💖 Credits

- **Author:** [YourName](https://github.com/YourName)
- **Inspired by:** The PocketMine-MP community

---

## ⭐ Support

If you like this plugin, please consider giving it a **star** on GitHub — it really helps!
