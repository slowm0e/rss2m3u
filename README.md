# rss2m3u

RSS podcast feeds to moOde audio player playlists — web UI, automatic M3U conversion via XSLT, stale feed cleanup, and episode titles with dates.

![moOde](https://img.shields.io/badge/moOde-audio%20player-green)
![License](https://img.shields.io/badge/license-GPL--3.0-blue)

## Features

- Web UI to manage RSS feeds and trigger manual runs
- Converts RSS feeds to `.m3u` playlists via XSLT
- Episode titles prefixed with date (`01.04 Episode title`)
- Stale feed cleanup — removes `.rss` files no longer in the feed list
- Triggers `mpc update` automatically after conversion
- Log viewer with colour-coded output

## Installation

**Dependencies**
```bash
sudo apt install xsltproc
```

**Files**
- Place `rss2m3u.php` in `/var/www/`
- Place all other files in `/var/www/util/rss2m3u/`

**Permissions**
```bash
sudo chown -R www-data:www-data /var/www/util/rss2m3u/
sudo chmod -R 755 /var/www/util/rss2m3u/
sudo chmod +x /var/www/util/rss2m3u/rss2m3u.sh
```

**Cron — run every day at 06:00**
```bash
sudo crontab -e
```
Add:
```
0 6 * * * /bin/bash /var/www/util/rss2m3u/rss2m3u.sh
```

**Visit**
```
http://<moode-ip>/rss2m3u.php
```

## Notes

- Cover images for podcasts must be added manually via moOde under **Playlists**
- Removing a feed from the UI will stop it being regenerated, but the `.m3u` must be removed manually via moOde under **Playlists**

## Credits

Inspired by [podcast2playlist](https://github.com/buzink/podcast2playlist) by buzink, originally written for Volumio. Adapted for moOde with a web UI and additional features.

A [fix for M3U title parsing](https://github.com/moode-player/moode/pull/740) was contributed to moOde as part of this project, which is required for episode titles to display correctly.
