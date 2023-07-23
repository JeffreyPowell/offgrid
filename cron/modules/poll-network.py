#!/usr/bin/python

import time
import datetime
import os

print ( "Starting poll-network.py " )

myPingAddress = {"name":"Google", "ip":"8.8.8.8"}

print("Pinging "+myPingAddress["name"]+" at "+myPingAddress["ip"] )

myRRDLogTime = datetime.datetime.now().strftime('%s')

myPingResult = os.popen("/bin/ping -c3 "+myPingAddress["ip"] ).read()
#print( myPingResult )

print("Ping complete")

################################################################

myPingAvgRTTFilter = "echo '"+myPingResult+"' | grep 'rtt min/avg/max/mdev' | awk '{ print $4 }' | awk -F\"/\" '{ print $2 }' | tr -d '\n'"
#print( myPingAvgRTTFilter )

myPingAvgRTT = os.popen( myPingAvgRTTFilter ).read()
#print ( "Avg RTT : ",myPingAvgRTT,"ms" )

myPingAvgRTTFloat = float( myPingAvgRTT )
print ( "Avg RTT : ",myPingAvgRTTFloat,"ms" )

myRRDData = myPingAvgRTTFloat

filename = "/home/sysadmin/offgrid/data/network_ping_"+myPingAddress["name"]+"_AvgRTT.rrd"

if( not os.path.exists( filename ) ):
        print ( "RRD does not exist. Creating : "+filename )
   
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
        print ( "Created." )
else:  
        print ( "RRD exist. Here : "+filename )

if( myRRDData != 'NULL' ):
        print ( "Writing data to : "+filename )
        os.system('/usr/bin/rrdtool update '+filename+" "+str(myRRDLogTime)+':'+str(myRRDData))
        print ( "done" )

#################################################

myPingDataFilter = "echo '"+myPingResult+"' | grep 'packets transmitted' | awk '{ print $6 }' | sed 's/%//'"
#print( myPingDataFilter )

myPingData = os.popen( myPingDataFilter ).read()
#print ( "Avg RTT : ",myPingAvgRTT,"ms" )

myPingDataFloat = float( myPingData )
print ( "Packet Loss : ",myPingDataFloat,"%" )

myRRDData = myPingDataFloat

filename = "/home/sysadmin/offgrid/data/network_ping_"+myPingAddress["name"]+"_Loss.rrd"

if( not os.path.exists( filename ) ):
        print ( "RRD does not exist. Creating : "+filename )
   
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
        print ( "Created." )
else:  
        print ( "RRD exist. Here : "+filename )

if( myRRDData != 'NULL' ):
        print ( "Writing data to : "+filename )
        os.system('/usr/bin/rrdtool update '+filename+" "+str(myRRDLogTime)+':'+str(myRRDData))
        print ( "done" )


print ( "Exiting poll-network.py ")

exit(1)