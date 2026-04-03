##Installation

#Dependencies: xsltproc
sudo apt install xsltproc

#Place rss2m3u.php in /var/www/
#Place all other files in /var/www/util/rss2m3u/rss2m3u.sh

#Permissions
sudo chown -R www-data:www-data /var/www/util/rss2m3u/
sudo chmod -R 755 /var/www/util/rss2m3u/
sudo chmod +x /var/www/util/rss2m3u/rss2m3u.sh

#Cron — run every day at 06:00
sudo crontab -e

#Add:
0 6 * * * /bin/bash /var/www/util/rss2m3u/rss2m3u.sh

#Visit http://<moode-ip>/rss2m3u.php