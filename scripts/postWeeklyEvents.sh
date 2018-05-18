#!/bin/bash
#
# This is an example command for a cron script to trigger the weekly email summary
# SITE_HOME and HTTP_X_FORWARDED_HOST must be set as environment variables for`
# the PHP script.
SITE_HOME=/srv/data/inroads HTTP_X_FORWARDED_HOST=bloomington.in.gov php postWeeklyEvents.php &> /var/log/cron/inroads_weekly
