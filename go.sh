#!/bin/bash

echo "[31mParsing Launchpad. This can take a while.[0m"
./activity.py > membership
echo "[31mSetting up datafile.[0m"
./chart.sh
gnuplot < gnuplot; eog plot_out.png
