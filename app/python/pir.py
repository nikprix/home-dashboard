#!/usr/bin/python

### import sys
import time
import RPi.GPIO as io
import subprocess
import datetime

io.setmode(io.BCM) # Choose BCM for GPIO address else BOARD for PIN address layout
io.setwarnings(False)
DAY_SHUTOFF_DELAY = 15*60  # seconds ## every 15 minutes
NIGHT_SHUTOFF_DELAY = 60  # seconds ## every 60 seconds
PIR_PIN = 4  # 7 on the board


timeStart = datetime.time(22, 0, 0)
timeEnd = datetime.time(23, 0, 0)
timeNow = datetime.datetime.now().time().strftime('%H:%M:%S')


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
            ### sys.stdout.flush() ### Release buffer to the terminal
            if not time_in_range(timeStart, timeEnd, timeNow) and turned_off:
                turned_off = False
                turn_on()
            if time_in_range(timeStart, timeEnd, timeNow) and turned_off:
                turned_off = False
                turn_on_HDMI()
        else:
            if not turned_off and time.time() > (last_motion_time + DAY_SHUTOFF_DELAY) and not time_in_range(timeStart, timeEnd, timeNow):
                turned_off = True
                turn_off()
            if not turned_off and time.time() > (last_motion_time + NIGHT_SHUTOFF_DELAY) and time_in_range(timeStart, timeEnd, timeNow):
                turned_off = True
                turn_off_HDMI()
        time.sleep(.1)

def turn_on():
    subprocess.call("sh /home/pi/Home_Dashboard/wake_Up_Screen.sh", shell=True)

def turn_on_HDMI():
    subprocess.call("sh /home/pi/Home_Dashboard/power_Screen_ON.sh", shell=True)

def turn_off():
    subprocess.call("sh /home/pi/Home_Dashboard/sleep_Screen.sh", shell=True)

def turn_off_HDMI():
    subprocess.call("sh /home/pi/Home_Dashboard/power_Screen_OFF.sh", shell=True)

def time_in_range(start, end, now):
    ### Return true if now is in the range [start, end]
    if start <= end:
        return start <= now <= end
    else:
        return start <= now or now <= end


if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        io.cleanup()
