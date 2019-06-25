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
    $default_width = 1000 ; $default_height = 150 ;
}
$device_id = $_GET['d'];
$period_span = $_GET['p'];
$chart_width = $_GET['w'];
$chart_height = $_GET['h'];
#$PERIOD ='-1y';
if( $period_span == "" )  { $period_span = '-8h';  $chart_width = header("Location: devices.php?p=$period_span&w=$chart_width&h=$chart_height"); exit; }
if( $chart_width == "" )  { $chart_width  = $default_width;  $chart_width = header("Location: devices.php?p=$period_span&w=$chart_width&h=$chart_height"); exit; }
if( $chart_height == "" ) { $chart_height = $default_height; $chart_width = header("Location: devices.php?p=$period_span&w=$chart_width&h=$chart_height"); exit; }
echo "<html><head>";
echo "<meta http-equiv=\"refresh\" content=\"30\">";
echo "</head><body bgcolor='#080808'>";
echo "<input type='button' onclick=\"location.href='devices.php?p=-1h&w=$chart_width&h=$chart_height';\" value='One Hour' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=-8h&w=$chart_width&h=$chart_height';\" value='Eight Hours' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=-1d&w=$chart_width&h=$chart_height';\" value='One Day' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=-1w&w=$chart_width&h=$chart_height';\" value='One Week' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=-1m&w=$chart_width&h=$chart_height';\" value='One Month' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=-1y&w=$chart_width&h=$chart_height';\" value='One Year' />";
echo "<br>";
$chart_bigger = strval( intval($chart_width) + 100);
$chart_smaller = strval( intval($chart_width) - 100);
$chart_taller = strval( intval($chart_height) + 50);
$chart_shorter = strval( intval($chart_height) - 50);
echo "<input type='button' onclick=\"location.href='devices.php?p=$period_span&w=$chart_bigger&h=$chart_height';\" value='Width +' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=$period_span&w=$chart_smaller&h=$chart_height';\" value='Width -' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=$period_span&w=$chart_width&h=$chart_taller';\" value='Height +' />";
echo "<input type='button' onclick=\"location.href='devices.php?p=$period_span&w=$chart_width&h=$chart_shorter';\" value='Height -' />";
echo "<br>";
#$config = parse_ini_file('/home/pi/offgrid/config.ini', true);
#$device_count = count($config['devices']['type']);
#for ($device_index=1; $device_index <= $device_count; $device_index++) {
for ($x = 0; $x <= 2; $x++) {
    
  #print_r( $device );
  #print_r( "===\n" );
  #$device_type    = (string) $config['devices']['type'][$device_index];
  #$device_ref      = (string) $config['devices']['ref'][$device_index];
  #$device_pin_num = (string) $config['devices']['pin'][$device_index];
  #$device_name    = (string) $config['devices']['name'][$device_index];
  #$device_units   = (string) $config['devices']['units'][$device_index];
  #$span           = '-12h';
  #print_r( $device_index );
  #print_r( $device_type );
  #print_r( $device_id );
  #print_r( $device_pin_num );
  #print_r( "---\n" );
  $img_name = 's-'.$x.'-'.$period_span.'-'.$chart_height.'x'.$chart_width;
  $rrd_name = 's-'.$x;
  $img_filename = '/var/www/offgrid/images/'.$img_name.'.png';
  $rrd_filename = '/home/pi/offgrid/data/'.$rrd_name.'.rrd';
  print_r( $rrd_filename );
  print_r( $img_filename );
  print_r( "***\n" );
  # create the rrd image
  create_graph( $rrd_filename, $img_filename,  $period_span, $device_name.' '.$period_span, $device_units, $chart_height, $chart_width);
  # display the image
  echo "<a href='device-dod.php?id=$x'>";
  echo "<img src='images/".$img_name.".png'>";
  echo "</a>";
}
echo "</body></html>";
exit;
function create_graph($input, $output, $start, $title, $units, $height, $width) {
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--vertical-label=$units",
#    "--lower=0",
    "--height=$height",
    "--width=$width",
    "-cBACK#161616",
    "-cCANVAS#1e1e1e",
    "-cSHADEA#000000",
    "-cSHADEB#000000",
    "-cFONT#c7c7c7",
    "-cGRID#888800",
    "-cMGRID#ffffff",
    "-nTITLE:10",
    "-nAXIS:9",
    "-nUNIT:10",
    "-y 0.2:5",
    "-cFRAME#ffffff",
    "-cARROW#000000",
    "DEF:dataavg=$input:data:AVERAGE",
    "CDEF:transdataavg=dataavg,1,*",
    "AREA:transdataavg#b6d14b40",
    "LINE4:transdataavg#a0b842:$title $units",
#    "COMMENT:\\n",
    "GPRINT:transdataavg:MIN: min %6.3lf",
    "GPRINT:transdataavg:MAX: max %6.3lf",
    "GPRINT:transdataavg:AVERAGE: avg %6.3lf",
    "GPRINT:transdataavg:LAST: last %6.3lf\\n"
  );
 $ret = rrd_graph($output, $options );
  if (! $ret) {
    echo "<b>Graph error: </b>".rrd_error()."\n";
  }
}
?>
