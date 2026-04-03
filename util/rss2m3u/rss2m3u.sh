#!/bin/bash
# File location: /var/www/util/rss2m3u/rss2m3u.sh

# Define paths
LOGFILE="/var/www/util/rss2m3u/log.txt"
FOLDER="/var/lib/mpd/playlists"

# Start logging session
echo "--- Run started: $(date) ---" >> "$LOGFILE"

cd /var/www/util/rss2m3u/ || { echo "ERROR: Could not cd to directory" >> "$LOGFILE"; exit 1; }

# Download rss feeds
while read -r p; do
    NAME="${p%;*}"
    URL="${p##*;}"
    echo "Downloading: $NAME" >> "$LOGFILE"
    if wget -q "$URL" -O "$NAME".rss; then
        echo "Success: $NAME" >> "$LOGFILE"
    else
        echo "ERROR: Failed to download $NAME" >> "$LOGFILE"
    fi
done < rssfeeds.txt

# Remove stale .rss files no longer in rssfeeds.txt
for rssfile in *.rss; do
    rssname="${rssfile%.rss}"
    if ! grep -q "^${rssname};" rssfeeds.txt; then
        echo "Removing stale: $rssfile" >> "$LOGFILE"
        rm -f "$rssfile"
    fi
done

# Convert rss feeds to m3u
shopt -s nullglob
for f in *.rss; do
    filename=$(basename "$f")
    filename="${filename%.*}"
    echo "Converting: $f" >> "$LOGFILE"
    xsltproc -o "$FOLDER"/"$filename".m3u m3u.xsl "$f" 2>> "$LOGFILE"
done

# Finalize and update moOde
echo "--- Run finished: $(date) ---" >> "$LOGFILE"
echo "--- Doing mpc update ---" >> "$LOGFILE"
mpc update >> "$LOGFILE" 2>&1

# Maintain 1000 line log limit
echo "$(tail -n 1000 "$LOGFILE")" > "$LOGFILE"
