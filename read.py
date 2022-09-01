import argparse
import datetime
import numpy as np
import os
import time
from urllib import request, parse
import serial
from picamera import PiCamera
from time import sleep
import datetime as dt
from PIL import Image, ImageDraw, ImageFont

while True:
    output = ''
    ser = serial.Serial('/dev/ttyACM0', 9600, 8, 'N', 1, timeout=0.5)
    while output == '':
        output = ser.readline()
    if output:
     try:
      print(output.decode(encoding="latin-1"))
      data = parse.urlencode({ "data": output}).encode()
      req =  request.Request("http://127.0.0.1/barcode.php", data=data) # this will make the method "POST"
      resp = request.urlopen(req)
      print(resp)
      camera = PiCamera()
      sleep(2)
      camera.resolution = (1024, 768)
      timenow = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
      camera.annotate_text = output.decode(encoding="latin-1")
      camera.capture('/var/www/html/cam/' + timenow + '.jpg')
      camera.close()
     except:
      print("An exception occurred")
