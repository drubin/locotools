#!/usr/bin/env python



app_name  = "LoCo Council Audit"
# This will tell LP what is running
# This should only be changed if that string is wrong.

output    = "./data/dataset"
# This will be combined with the team name to set up
# the output file ( output-team-name )


##
## Don't edit below here unless you know what you are doing
############################################################

from launchpadlib.launchpad import Launchpad
from launchpadlib.errors import HTTPError

import time
import datetime
import os
import json
import sys

server    = 'edge'
cachedir  = os.path.expanduser("~/.launchpadlib/cache")

print "Enter the Launchpad team name, plox"

team = sys.stdin.readline().strip()


output = output + "-" + team

print "Connecting to Launchpad..."

# launchpad = Launchpad.login_anonymously(app_name, server, cachedir)
launchpad = Launchpad.login_with(app_name, server)

print "Fetching Team"

team = launchpad.people[team]

membership = {}

print "Fetching Details"

members = team.members_details

d_mini = 0  # data min
d_maxi = 0  # data max
k_mini = -1 # karma min
k_maxi = 0  # karma max

rolling_karma = -1

print "Starting to Process..."

count = 0
for member in members:
    try:
        d = member.date_joined
        posix_join_date = time.mktime(d.timetuple())
        membership[member.member.name] = { "join" : posix_join_date, "karma" : member.member.karma }

	karma = member.member.karma

        if k_mini > karma or k_mini == -1:
            k_mini = karma
        if k_maxi < karma:
            k_maxi = karma

        if rolling_karma == -1:
            rolling_karma = karma
        else:
            rolling_karma = ( rolling_karma + karma ) / 2

        if d_mini > posix_join_date or d_mini == 0:
            d_mini = posix_join_date
        if d_maxi < posix_join_date:
            d_maxi = posix_join_date

        print "  Processed " + member.member.name + "  ( %04d )" % count
        count = count +1 
    except HTTPError, e:
	print "HTTP Error"

print "Start date: " + str(d_mini) + ", end date " + str(d_maxi)
print "Karma avg: " + str(rolling_karma) + ", max: " + str(k_maxi) + ", min: " + str(k_mini)

print "Writitng Data to ", output
f = open(output, 'w')
f.write(json.dumps(membership))
print "Wrote data! All set."

