#!/usr/bin/env python


app_name  = "LoCo Council Audit"
# This will tell LP what is running
# This should only be changed if that string is wrong.

output    = "./data/dataset"
# This will be combined with the team name to set up
# the output file ( output-team-name )


team      = "locoteams-approved"
# Search for expiring members of this team.
# This should work with any LP team.

week_time = 24
# How many weeks in the future to use as a threshold.


warning   = 14 # Two weeks
critical  = 7  # One week


##
## Don't edit below here unless you know what you are doing
############################################################

from launchpadlib.launchpad import Launchpad
from launchpadlib.errors import HTTPError

import time
import datetime
from datetime import date

import os
import json
import sys


server    = 'edge'
cachedir  = os.path.expanduser("~/.launchpadlib/cache")

print "Connecting to LP -- Fill out the forms if the pop up."

# launchpad = Launchpad.login_anonymously(app_name, server, cachedir)
launchpad = Launchpad.login_with(app_name, server)

print "Fetching Team ( " + team + " )"

team = launchpad.people[team]

membership = {}

print "Fetching Details"

members = team.members_details

print "We have details. Starting to Process..."

membership = {}

count = 0;

warning_spool  = ""
critical_spool = ""
clean_spool    = ""

for member in members:
    try:
        d = member.date_expires
        if d != None:
            expire = d.date()
            today  = date.fromtimestamp(time.time())

            ttl = ( expire - today )

            membership[member.member.name] = {}
            membership[member.member.name][0] = member.member.name
            membership[member.member.name][1] = ttl.days

            flag = " "

            if ttl.days < critical:
                flag = "!"
                critical_spool = critical_spool + member.member.name + "\n"

            elif ttl.days < warning:
                flag = "+"
                warning_spool = warning_spool + member.member.name + "\n"
            else:
                clean_spool = clean_spool + member.member.name + "\n"

            print "  Processed " + flag + " " + member.member.name + ", " + str(ttl.days) + "  ( %04d )" % count
            count = count + 1
        else:
            print "  " + member.member.name + " does not expire"

    except HTTPError, e:
	print "  Error! We've had an HTTP Error"

print "\n\n"
print "Warnings:"
print warning_spool

print "\n\n"
print "Criticals:"
print critical_spool

#print "\n\n"
#print "Clean:"
#print clean_spool

