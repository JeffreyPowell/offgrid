<?php
//Detect special conditions devices
$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
//do something with this information
if( $iPod || $iPhone ){
    $default_width = 300 ; $default_height = 100 ;
}else if($iPad){
    $default_width = 800 ; $default_height = 200 ;
}else if($Android){
    $default_width = 300 ; $default_height = 100 ;
}else if($webOS){
    $default_width = 300 ; $default_height = 100 ;
}else{
    $default_width = 1000 ; $default_height = 500 ;
}
$default_min = 10 ;
$default_max = 15 ;
$device_index = $_GET['id'];
$chart_width = $_GET['w'];
$chart_height = $_GET['h'];
$chart_min = $_GET['min'];
$chart_max = $_GET['max'];
if( $device_index == "" ) { $device_index = '1';             header("Location: device-dod.php?id=$device_index&w=$chart_width&h=$chart_height&min=$chart_min&max=$chart_max"); exit; }
if( $chart_width  == "" ) { $chart_width  = $default_width;  header("Location: device-dod.php?id=$device_index&w=$chart_width&h=$chart_height&min=$chart_min&max=$chart_max"); exit; }
if( $chart_height == "" ) { $chart_height = $default_height; header("Location: device-dod.php?id=$device_index&w=$chart_width&h=$chart_height&min=$chart_min&max=$chart_max"); exit; }
if( $chart_min == "" ) { $chart_min = $default_min; header("Location: device-dod.php?id=$device_index&w=$chart_width&h=$chart_height&min=$chart_min&max=$chart_max"); exit; }
if( $chart_max == "" ) { $chart_max = $default_max; header("Location: device-dod.php?id=$device_index&w=$chart_width&h=$chart_height&min=$chart_min&max=$chart_max"); exit; }
echo "<html><head>";
echo "<meta http-equiv=\"refresh\" content=\"240\">";
echo "<style type='text/css'>a {text-decoration: none}</style>";
echo "</head><body bgcolor='#080808'>";
echo "<a href='devices.php' style='font-family:helvetica;font-size:20px;color:grey;'>All Devices</a><br>";
echo "<br>";
$chart_bigger = strval( intval($chart_width) + 100);
$chart_smaller = strval( intval($chart_width) - 100);
$chart_taller = strval( intval($chart_height) + 50);
$chart_shorter = strval( intval($chart_height) - 50);
echo "<input type='button' onclick=\"location.href='device-dod.php?id=$device_index&w=$chart_bigger&h=$chart_height';\" value='Width +' />";
echo "<input type='button' onclick=\"location.href='device-dod.php?id=$device_index&w=$chart_smaller&h=$chart_height';\" value='Width -' />";
echo "<input type='button' onclick=\"location.href='device-dod.php?id=$device_index&w=$chart_width&h=$chart_taller';\" value='Height +' />";
echo "<input type='button' onclick=\"location.href='device-dod.php?id=$device_index&w=$chart_width&h=$chart_shorter';\" value='Height -' />";
echo "<br>";
$config = parse_ini_file('/home/pi/bin/pi-van-mon/www/config.ini', true);
$device_count = count($config['devices']['type']);
echo "<select onChange='window.location.href=this.value'>";
for ($device_loop=1; $device_loop <= $device_count; $device_loop++) {
  $device_type    = (string) $config['devices']['type'][$device_loop];
  $device_ref     = (string) $config['devices']['ref'][$device_loop];
  $device_pin_num = (string) $config['devices']['pin'][$device_loop];
  $device_name    = (string) $config['devices']['name'][$device_loop];
  $device_units   = (string) $config['devices']['units'][$device_loop];
  if( $device_loop == $device_index ){
    echo "<option value='device-dod.php?id=$device_loop&w=$chart_width&h=$chart_height' selected>$device_name</option>";
    }else{
    echo "<option value='device-dod.php?id=$device_loop&w=$chart_width&h=$chart_height'>$device_name</option>";
    }
  }
echo "</select>";
if( $device_index > $device_count ) { exit; }
$device_type    = (string) $config['devices']['type'][$device_index];
$device_ref     = (string) $config['devices']['ref'][$device_index];
$device_pin_num = (string) $config['devices']['pin'][$device_index];
$device_name    = (string) $config['devices']['name'][$device_index];
$device_units   = (string) $config['devices']['units'][$device_index];
$img_name = $device_type.'-'.$device_ref.'-'.$device_pin_num.'-dod-'.$chart_height.'x'.$chart_width;
$rrd_name = $device_type.'-'.$device_ref.'-'.$device_pin_num;
print $device_pin_num;
echo "<br>:$device_pin_num:<br>";
$img_filename = '/var/www/pi-van-mon/images/'.$img_name.'.png';
$rrd_filename = '/home/pi/bin/pi-van-mon/data/'.$rrd_name.'.rrd';
#create_graph_dayonday($DEVICEID, "images/d-dayonday-temp-$DEVICEID.png", "temp",    "( C )",      "AVERAGE", $width, $height);
#echo "<img src='images/d-dayonday-temp-$DEVICEID.png'>";
create_graph_dayonday( $rrd_filename, $img_filename, $device_name.' Day on Day', $device_units, "AVERAGE",$chart_height, $chart_width, $chart_min, $chart_max);
echo "<img src='images/".$img_name.".png' alt='Generated RRD image'><br><br>";
echo "</body></html>";
exit;
function create_graph_dayonday($inputrrd, $outputimg, $dataname, $dataunit, $datacf, $height, $width, $ymin, $ymax) {
 $red          = "FF0000";
 $red_dark     = "880000";
 $orange       = "FFA500";
 $yellow       = "FFFF00";
 $yellow_dark  = "888800";
 $green        = "00FF00";
 $blue         = "00FFFF";
 $indigo       = "0000FF";
 $violet       = "8D38C9";
 $black        = "000000";
 $grey_dark_vv = "161616";
 $grey_dark_v  = "1e1e1e";
 $grey_light   = "c7c7c7";
 $white        = "ffffff";
 $days = array(
   0 => $red,
   1 => $orange,
   2 => $yellow,
   3 => $green,
   4 => $blue,
   5 => $indigo,
   6 => $violet
 );
 $titlept=9;
 $linewidth=4;
 $vertaxislabelpt=6;
 $allaxisvaluept=10;
 $legendpt=8;
 $options = array(
   "--title=$dataname",
  # "--alt-y-grid",
  # "--alt-autoscale",
  # "-y 0.2:10",
   "--lower-limit=$ymin",
   "--upper-limit=$ymax",
   "--rigid",
   "--slope-mode",
   "--end=midnight",
   "--start=end-1d",
   "--vertical-label=$dataunit",
   "--height=$height",
   "--width=$width",
   "-cBACK#$grey_dark_vv",
   "-cSHADEA#$black",
   "-cSHADEB#$black",
   "-cFONT#$grey_light",
   "-cCANVAS#$grey_dark_v",
   "-cGRID#$yellow_dark",
   "-cMGRID#$white",
   "-cFRAME#$white",
   "-cARROW#$black",
   "-nTITLE:$titlept",
   "-nLEGEND:$legendpt",
   "-nAXIS:$allaxisvaluept",
   "-nUNIT:$vertaxislabelpt"
 );
 $offset = 60*60*24;
 for ( $DAYID = 6; $DAYID >=0; $DAYID -- ) {
   $DAYNAME  = date( 'D', mktime(0, 0, 0, date("m")  , date("d")-$DAYID, date("Y")));
   $linecol  = $days[$DAYID]."FF";
   $areacol  = $days[$DAYID]."20";
   $DAYSTART = strval(0-$DAYID);
   $DAYEND   = strval(1-$DAYID);
   $DAYSHIFT = strval(($DAYID-1)*$offset);
   $DAYIDST  = strval($DAYID);
   if( $DAYSTART >= 0 ) $DAYSTART ="+".$DAYSTART;
   if( $DAYEND   >= 0 ) $DAYEND   ="+".$DAYEND;
   $DAYSTART = $DAYSTART."d";
   $DAYEND   = $DAYEND."d";
   array_push($options,
     "DEF:a".$DAYIDST."=$inputrrd:data:".$datacf.":start=midnight".$DAYSTART.":end=midnight".$DAYEND,
     "CDEF:b$DAYIDST=a$DAYIDST,1,*",
     "AREA:b$DAYIDST#$areacol"
#      "LINE$linewidth:b$DAYIDST#$linecol:$DAYNAME",
#      "SHIFT:b".$DAYIDST.":".$DAYSHIFT
     );
 }
 for ( $DAYID = 6; $DAYID >=0; $DAYID -- ) {
   $DAYNAME  = date( 'D', mktime(0, 0, 0, date("m")  , date("d")-$DAYID, date("Y")));
   $linecol  = $days[$DAYID]."CC";
   $areacol  = $days[$DAYID]."20";
   $DAYSTART = strval(0-$DAYID);
   $DAYEND   = strval(1-$DAYID);
   $DAYSHIFT = strval(($DAYID-1)*$offset);
   $DAYIDST  = strval($DAYID);
   if( $DAYSTART >= 0 ) $DAYSTART ="+".$DAYSTART;
   if( $DAYEND   >= 0 ) $DAYEND   ="+".$DAYEND;
   $DAYSTART = $DAYSTART."d";
   $DAYEND   = $DAYEND."d";
   array_push($options,
     "LINE$linewidth:b$DAYIDST#$linecol:$DAYNAME",
     "SHIFT:b".$DAYIDST.":".$DAYSHIFT,
     "GPRINT:b$DAYIDST:MIN:MIN %6.2lf",
     "GPRINT:b$DAYIDST:MAX:MAX %6.2lf",
     "GPRINT:b$DAYIDST:LAST:LAST %6.2lf\\n"
     );
 }
$ret = rrd_graph($outputimg, $options);
 if (! $ret) {
   echo "<b>Graph error: </b>".rrd_error()."\n";
 }
}
