#!/usr/bin/env python

import sys
import time
import RPi.GPIO as io
import subprocess

io.setmode(io.BCM)
SHUTOFF_DELAY = 60 # seconds
PIR_PIN = 4       # 7 on the board

def main():
    io.setup(PIR_PIN, io.IN)
    turned_off = False
    last_motion_time = time.time()

    while True:
        if io.input(PIR_PIN):
            last_motion_time = time.time()
            print ".",
            sys.stdout.flush()
            if turned_off:
                turned_off = False
                turn_on()
        else:
            if not turned_off and time.time() > (last_motion_time + SHUTOFF_DELAY):
                turned_off = True
                turn_off()
            if not turned_off and time.time() > (last_motion_time + 1):
            # io.output(LED_PIN, io.HIGH)
        time.sleep(.1)

def turn_on():
    subprocess.call("sh /home/pi/Home_Dashboard/power_Screen_ON.sh", shell=True)

def turn_off():
    subprocess.call("sh /home/pi/Home_Dashboard/power_Screen_OFF.sh", shell=True)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        io.cleanup()