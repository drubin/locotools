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

print "I'll count the groups on a team, just for you"
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

d_mini = 0 # data min
d_maxi = 0 # data max

print "Starting to Process..."

count = 0
c1    = 0

for member in members:
    try:
	if member.member.is_team:
		print "Team: " + member.member.name + "  ( %04d )" % count
        	count = count + 1 
	c1 = c1 + 1
    except HTTPError, e:
	print "HTTP Error"


print "Number of teams: " + str(count) + " out of " + str(c1) + " members."
