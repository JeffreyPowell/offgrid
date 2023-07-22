#!/usr/bin/python

from ABElectronics_ADCPi import ADCPi
import time
import datetime
import os

# Sample rate can be 12,14, 16 or 18
adc = ADCPi(0x6a, 0x6b, 12)


x1 = 769.0 / 12.91
x2 = 773.0 / 12.91
x3 = 768.0 / 12.91
x4 = 767.0 / 12.86

#x1=x2=x3=1

y = 1.00
z = 1.00

rate = 0.01 # seconds
samples = 1 # samples

#print ("debug 2")

for i in range(0, 1):

        t = datetime.datetime.now().strftime('%s')

        v1 = 0
        v2 = 0
        v3 = 0
        v4 = 0
        v5 = 0
        v6 = 0
        v7 = 0

        v8 = 0

        for j in range(0, samples):

                v1 = v1 + adc.readRaw(1)
                v2 = v2 + adc.readRaw(2)
                #v3 = v3 + adc.readRaw(3)
                #v4 = v4 + adc.readRaw(4)
                #v5 = v5 + int("%8.0f" % (adc.readRaw(5)/3))
                #v6 = v6 + int("%8.0f" % (adc.readRaw(6)/3))
                #v7 = v7 + int("%8.0f" % (adc.readRaw(7)/3))
                #v8 = v8 + int("%8.0f" % (adc.readRaw(8)/3))
                time.sleep(rate)

        v1 = v1 / samples
        v2 = v2 / samples
        #v3 = v3 / samples
        #v4 = v4 / samples
        #v5 = v5 / samples
        #v6 = v6 / samples
        #v7 = v7 / samples
        #v8 = v8 / samples

        s1 = "%2.3f" % (   v1    /x1 )
        s2 = "%2.3f" % (   v2    /x2 )
        #s3 = "%2.3f" % (   v3    /x3 )
        #s4 = "%2.3f" % (   v4    /x4 )
        #s5 = "%4.4f" % ( ( v5-z )/y )
        #s6 = "%4.4f" % ( ( v6-z )/y )
        #s7 = "%4.4f" % ( ( v7-z )/y )
        #s8 = "%4.4f" % ( ( v8-z )/y )

#os.system('/usr/bin/rrdtool update /usr/local/scripts/git/pi-adc-mon/data/adc-volts.rrd `date +"%s"`:$V1:$V2:$V3:$V4:$V5:$V6:$V7:$V8')

print(str(s1))

print(str(s2))

#os.system('/usr/bin/rrdtool update /home/pi/offgrid/data/adc-volts-1.rrd '+str(t)+':'+str(s1))
#os.system('/usr/bin/rrdtool update /home/pi/offgrid/data/adc-volts-2.rrd '+str(t)+':'+str(s2))

sensor_id = 1
data = str(s1)

filename = '/home/pi/offgrid/data/s-'+str(sensor_id)+'.rrd'
  
print (filename)

if( not os.path.exists( filename ) ):
        print ( os.path.exists( filename ))
        os.system('/usr/bin/rrdtool create '+filename+' --step 60 \
        --start now \
        DS:data:GAUGE:120:U:U \
        RRA:MIN:0.5:1:10080 \
        RRA:MIN:0.5:5:51840 \
        RRA:MIN:0.5:60:8760 \
        RRA:AVERAGE:0.5:1:10080 \
        RRA:AVERAGE:0.5:5:51840 \
        RRA:AVERAGE:0.5:60:8760 \
        RRA:MAX:0.5:1:10080 \
        RRA:MAX:0.5:5:51840 \
        RRA:MAX:0.5:60:8760')

if( data != 'NULL' ):
        print("rrd")
        os.system('/usr/bin/rrdtool update '+filename+" "+str(t)+':'+data)
        
sensor_id = 2
data = str(s2)

filename = '/home/pi/offgrid/data/s-'+str(sensor_id)+'.rrd'
  
print (filename)

if( not os.path.exists( filename ) ):
        print ( os.path.exists( filename ))
        os.system('/usr/bin/rrdtool create '+filename+' --step 60 \
        --start now \
        DS:data:GAUGE:120:U:U \
        RRA:MIN:0.5:1:10080 \
        RRA:MIN:0.5:5:51840 \
        RRA:MIN:0.5:60:8760 \
        RRA:AVERAGE:0.5:1:10080 \
        RRA:AVERAGE:0.5:5:51840 \
        RRA:AVERAGE:0.5:60:8760 \
        RRA:MAX:0.5:1:10080 \
        RRA:MAX:0.5:5:51840 \
        RRA:MAX:0.5:60:8760')

if( data != 'NULL' ):
        print("rrd")
        os.system('/usr/bin/rrdtool update '+filename+" "+str(t)+':'+data)
 
