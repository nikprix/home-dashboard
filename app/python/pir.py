#!/usr/bin/python

import sys
import time
import RPi.GPIO as io
import subprocess

io.setmode(io.BCM) # Choose BCM for GPIO address else BOARD for PIN address layout
io.setwarnings(False)
SHUTOFF_DELAY = 20 # 15*60  # seconds ## every 15 minutes
PIR_PIN = 4  # 7 on the board

# DEBUG: see user that's running this script
# print('script running as: ')
# subprocess.call('whoami', shell=True)

def main():
    io.setup(PIR_PIN, io.IN)
    turned_off = False
    last_motion_time = time.time()

    while True:
        if io.input(PIR_PIN):
            last_motion_time = time.time()
            print("Motion detected!")  ### DEBUG
            sys.stdout.flush() ### Release buffer to the terminal
            if turned_off:
                turned_off = False
                turn_on()
        else:
            if not turned_off and time.time() > (last_motion_time + SHUTOFF_DELAY):
                turned_off = True
                turn_off()
        time.sleep(.1)

def turn_on():
    subprocess.call("sh /home/pi/Home_Dashboard/wake_Up_Screen.sh", shell=True)


def turn_off():
    subprocess.call("sh /home/pi/Home_Dashboard/sleep_Screen.sh", shell=True)


if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        io.cleanup()
