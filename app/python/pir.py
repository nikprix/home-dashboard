#!/usr/bin/python

# import sys
import time
import RPi.GPIO as io
import subprocess

io.setmode(io.BCM) # Choose BCM for GPIO address else BOARD for PIN address layout
io.setwarnings(False)
SHUTOFF_DELAY = 10 # 15*60  # seconds
PIR_PIN = 4  # 7 on the board
### Below is the script's termination time that used together with cron job.
### For example, cron executes this script at 6:00 in the morning, script runs the whole day
### and exits at midnight (24:00)
TERMINATE_TIME = 60 # 66600 # (18*60*60+30*60) # Terminate script after 18 hours and 30 minutes
start = time.time()

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
            ### print("Motion detected!")  ### DEBUG
            ### print ".",   ### DEBUG
            ### sys.stdout.flush() ### Release buffer to the terminal
            if turned_off:
                turned_off = False
                turn_on()
        else:
            if not turned_off and time.time() > (last_motion_time + SHUTOFF_DELAY):
                turned_off = True
                turn_off()
        time.sleep(.1)
        # exiting while loop after 18 hours and 30 minutes
        if time.time() > start + TERMINATE_TIME : break


def turn_on():
    subprocess.call("sh /home/pi/Home_Dashboard/wake_Up_Screen.sh", shell=True)


def turn_off():
    subprocess.call("sh /home/pi/Home_Dashboard/sleep_Screen.sh", shell=True)


if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        io.cleanup()
