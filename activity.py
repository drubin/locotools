#!/usr/bin/env python



app_name  = "LoCo Council Audit"
# This will tell LP what is running
# This should only be changed if that string is wrong.

team      = "ubuntu-ru"
# Team to audit

output    = "dataset"
# This will be combined with the team name to set up
# the output file ( output-team-name )




##
## Don't edit below here unless you know what you are doing
############################################################

from launchpadlib.launchpad import Launchpad

import time
import datetime
import os
import json

server    = 'edge'
cachedir  = os.path.expanduser("~/.launchpadlib/cache")

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

for member in members:
    d = member.date_joined
    membership[member.member.name] = time.mktime(d.timetuple())
    print "  Processed", member.member.name

print "Writitng Data to ", output
f = open(output, 'w')
f.write(json.dumps(membership))
print "Wrote data! All set."

