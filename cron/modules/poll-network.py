#!/usr/bin/python

import time
import datetime
import os



myLogTime = datetime.datetime.now().strftime('%s')

myPingOne = "86.12.213.7"
myPingTwo = "8.8.8.8"



myPingOneLoss   = os.system("/bin/ping -c3 "+myPingOne+" | grep 'packets transmitted' | awk '{ print $6 }' | sed 's/%//'")


print (myLogTime)

#print ("Loss : ",myPingOneLoss,"%")


myPingTwoResult = os.popen("/bin/ping -c3 "+myPingOne).read()


myPingTwoLoss   = float( os.popen("echo '"+myPingTwoResult+"' | grep 'packets transmitted' | awk '{ print $6 }' | sed 's/%//'" ).read() )

myPingTwoAvgRTTFilter = "echo '"+myPingTwoResult+"' | grep 'rtt min/avg/max/mdev' | awk '{ print $4 }' | awk -F\"/\" '{ print $2 }' | tr -d '\n'"

#print(myCommand)

myPingTwoAvgRTT = float( os.popen( myPingTwoAvgRTTFilter ).read() )



#print ( myPingTwoResult )
#print ("-")
#print ( myPingTwoLoss )

print ("Loss    : ",myPingTwoLoss,"%")

print ("Avg RTT : ",myPingTwoAvgRTT,"ms")


quit()

filename = '../data/network_ping_home.rrd'

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

if( myData != 'NULL' ):
        #print"rrd"
        os.system('/usr/bin/rrdtool update '+filename+" "+str(myLogTime)+':'+str(myData))
