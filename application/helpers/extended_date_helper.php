<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( !function_exists('get_select_months') )
{
function get_select_months(){
    $currMonth = date('n');
    $currYear = date('Y');
    $startYear = '2014';
    for ($y = $currYear; $y >= $startYear; $y--):
      if ($y == $currYear):
        $endMonth = $currMonth;
      else: $endMonth = 12;
      endif;
      if($y==2014){ $s=4; } else {$s=1; }
      for ($x = $endMonth; $x >= $s; $x--):
        $months[$y . "-" . $x . "-01"] = date('F Y', strtotime($y . "-" . $x . "-01"));
      endfor;
    endfor;
	return $months;	
}
}

if ( !function_exists('to_mysql_datetime') )
{
    function to_mysql_datetime($datetime)
    {
        if (substr_count($datetime, "/") > 0)
        {	$time="";
			$parts = explode(" ", $datetime);
			$date=$parts[0];
			if(isset($parts[1])&&!empty($parts[1])){
			$time=$parts[1];
			}
            $pieces = explode('/', $date);
			$result = $pieces[2] . '-' . $pieces[1] . '-' . $pieces[0] . " " . $time;
            return $result;
			
        }
        return false;
    }   
}
if ( !function_exists('sql_date_check') )
{
    /**
     * date_is_future. Checks that the date supplied is exclusively in the future
     * 
     * @param date $date
     * @return boolean true if the date is in the future
     */
    function sql_date_check($date)
    {
        $testDate = new DateTime($date);
        $currDate = new DateTime(date('Y-m-d H:i:s')); //Disregard the time component
        
        if ($testDate > $currDate) {
            return true;
        }
        return false;
    }   
}

if ( !function_exists('date_is_future') )
{
    /**
     * date_is_future. Checks that the date supplied is exclusively in the future
     * 
     * @param date $date
     * @return boolean true if the date is in the future
     */
    function date_is_future($date)
    {
        $testDate = new DateTime(to_mysql_datetime($date));
        $currDate = new DateTime(date('Y-m-d')); //Disregard the time component
        
        if ($testDate > $currDate) {
            return true;
        }
        return false;
    }   
}

if ( !function_exists('date_is_past') )
{
    /**
     * date_is_past method. Checks if the date supplied exclusively is in the past.
     * Different from !date_is_future because this will include todays date
     * 
     * @param date $date
     * @return boolean true if the date is in the past
     */
    function date_is_past($date)
    {
        $testDate = new DateTime(to_mysql_datetime($date));
        $currDate = new DateTime(date('Y-m-d')); //Disregard the time component

        if ($testDate < $currDate) {
            return true;
        }
        return false;
    }   
}

if ( !function_exists('is_valid_date_range') )
{
    function is_valid_date_range($dateFrom, $dateTo)
    {
        $dateFrom = new DateTime($dateFrom);
        $dateTo   = new DateTime($dateTo);
        
        if ($dateTo >= $dateFrom) {
            return true;
        }
        return false;
    }   
}


if ( !function_exists('time_elapsed_string') )
{

function time_elapsed_string($ptime,$stime='')
{
    if(empty($stime)){
    $etime = time() - $ptime;
    }
    else {
        $etime = $stime - $ptime;
    }
    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}

}

if ( !function_exists('time_togo_string') )
{

function time_togo_string($ptime)
{
    $etime = $ptime - time() ;

    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '');
        }
    }
}

if ( !function_exists('date_sort') )
{
function date_sort($a, $b)
{
    if ($a['date'] == $b['date']) {
        return 0;
    }

    return ($a['date'] < $b['date']) ? -1 : 1;
}
}

// Elapsed Time
function elapsed_time($seconds = 1) {
  if (!is_numeric($seconds)) {
    $seconds = strtotime($seconds);
  }
  $seconds = now() - $seconds;
  
  $days = floor($seconds / 86400);
  if ($days > 0) {
      if ($days == 1) {
          return '1 day ago';
      }
    return $days . ' days ago';
  }
  
  $hours = floor($seconds / 3600);
  if ($hours > 0) {
    if ($hours == 1) {
      return '1 hour ago';
    }
    return $hours . ' hours ago';
  }
  
  $minutes = floor($seconds / 60);
  if ($minutes > 0) {
    if ($minutes == 1) {
      return '1 minute ago';
    }
    return $minutes . ' minutes ago';
  }
  
  if ($seconds <= 1) {
    return 'Just now';
   } else {
    return $seconds . ' seconds ago';
  }
} 

}
/* End of file extended_date_helper.php */
/* Location: ./application/helpers/extended_date_helper.php */

