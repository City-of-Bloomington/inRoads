# This triggers the weekly email to the inRoads Google Group
00 15   * * 5   www-data  SITE_HOME={{ inroads_site_home }} HTTP_X_FORWARDED_HOST=bloomington.in.gov php {{ inroads_install_path }}/scripts/postWeeklyEvents.php &> /var/log/cron/inroads_weekly
