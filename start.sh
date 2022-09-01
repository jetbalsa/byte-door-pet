#!/bin/bash
while true; do
python3 /var/www/html/read.py | logger -s
sleep 1;
done
