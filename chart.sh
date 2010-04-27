#!/bin/bash

COUNT=$(./anyl.sh)
FOO=$(echo $COUNT | tr " " "\n" | sort -n | uniq)

FIRST=$(echo $FOO | tr " " "\n" | head -n 1)
LAST=$(echo $FOO | tr " " "\n" | tail -n 1)

LIST=""

if [ -e 'data' ]; then
	rm data
fi
touch data

for a in $FOO; do
	RES=$(echo $COUNT | tr " " "\n" | grep $a | wc -l)
	echo "$a $RES" >> data
	LIST="$LIST $RES"
done

# echo $LIST | tr " " "\n" | sort -n

LFIRST=$(echo $LIST | tr " " "\n" | sort -n | head -n 1)
LLAST=$(echo $LIST | tr " " "\n" | sort -n | tail -n 1)

let LLAST=$LLAST+10

echo "set xrange[$FIRST:$LAST]" > gnuplot
echo "set yrange[0:$LLAST]" >> gnuplot
echo "set xlabel 'Year'" >> gnuplot
echo "set ylabel 'Membership'" >> gnuplot
echo "set term png xFFFFFF medium" >> gnuplot
echo "set output 'plot_out.png'" >> gnuplot
echo "plot 'data' title 'Membership Plot' with linespoints" >> gnuplot
