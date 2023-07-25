#!/usr/bin/python

import time
import datetime
import os

print ( "Starting poll-time.py " )

now = time.time()

t = datetime.datetime.now().strftime('%s')



seconds    = time.strftime("%S", time.gmtime(now))
minute     = time.strftime("%M", time.gmtime(now))
hour       = time.strftime("%H", time.gmtime(now))

dayofyear  = time.strftime("%j", time.gmtime(now))
dayofmonth = time.strftime("%d", time.gmtime(now))
dayofweek  = time.strftime("%w", time.gmtime(now))
week       = time.strftime("%W", time.gmtime(now))

month      = time.strftime("%m", time.gmtime(now))
year       = time.strftime("%Y", time.gmtime(now))



#print (seconds)
print ( "Minute : ", minute, "min" )
#print (hour)
#print (t)



myRRDData = minute

filename = "/home/sysadmin/offgrid/data/time-minutes.rrd"

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

if( seconds != 'NULL' ):
        #print"rrd"
        os.system('/usr/bin/rrdtool update '+filename+" "+str(t)+':'+str(myRRDData))

print ( "Exiting poll-time.py ")

exit(1)
