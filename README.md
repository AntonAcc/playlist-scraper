Music Playlist Scraper
======================

Build playlist-scraper docker image
-----------------------------------
docker build -t playlist-scraper .

Prepare output dir
------------------
sudo mkdir /var/playlist_scraper_output
sudo chmod 777 -R /var/playlist_scraper_output

Manual run
----------
docker run -u www-data --privileged --rm -t --name playlist-scraper -v /var/playlist_scraper_output:/app/output \
    playlist-scraper /app/bin/console app:scrape

Setup autorun
-------------
sudo bash -c 'echo "*/3 * * * * root docker run -u www-data --privileged --rm -t --name playlist-scraper \
    -v /var/playlist_scraper_output:/app/output playlist-scraper /app/bin/console app:scrape -vvvvv  \
    >> /var/log/playlist-scraper.log 2>&1" > /etc/cron.d/playlist-scraper'

Contributing
------------
You are able to create any pull request and ask for merging it

License
-------
Licensed under the MIT license