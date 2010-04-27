#!/usr/bin/env python

from launchpadlib.launchpad import Launchpad
import time

cachedir = "/home/tag/.launchpadlib/cache"

launchpad = Launchpad.login_anonymously('LoCoCouncil Activity Audit', 'production', cachedir)

team = "ubuntu-lococouncil"

team = launchpad.people[team]
members = team.members_details
for member in members:
    print member.member.name + "," + str(member.date_joined)
