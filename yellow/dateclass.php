<?php
class Date 
{
	/***
		$from_mask can be:
			s	seconds, with leading zeros
			i	minutes, with leading zeros
			m	month, with leading zeros
			H	24-hour format of an hour with leading zeros
			y	A two digit represenation of a year
			Y	A full numeric represenation of a year, 4 digits
		@author Mariusz Stankiewicz http://prettymad.net
	*/
	function convert($string, $from_mask, $to_mask='', $return_unix=false)
	{
		// define the valid values that we will use to check
		// value => length
		$all = array(
			's' => 'ss',
			'i' => 'ii',
			'H' => 'HH',
			'y' => 'yy',
			'Y' => 'YYYY', 
			'm' => 'mm', 
			'd' => 'dd'
		);

		// this will give us a mask with full length fields
		$from_mask = str_replace(array_keys($all), $all, $from_mask);

		$vals = array();
		foreach($all as $type => $chars)
		{
			// get the position of the current character
			if(($pos = strpos($from_mask, $chars)) === false)
				continue;

			// find the value in the original string
			$val = substr($string, $pos, strlen($chars));

			// store it for later processing
			$vals[$type] = $val;
		}

		foreach($vals as $type => $val)
		{
			switch($type)
			{
				case 's' :
					$seconds = $val;
				break;
				case 'i' :
					$minutes = $val;
				break;
				case 'H':
					$hours = $val;
				break;
				case 'y':
					$year = '20'.$val; // Year 3k bug right here
				break;
				case 'Y':
					$year = $val;
				break;
				case 'm':
					$month = $val;
				break;
				case 'd':
					$day = $val;
				break;
			}
		}

		$unix_time = mktime(
			(int)$hours, (int)$minutes, (int)$seconds, 
			(int)$month, (int)$day, (int)$year);
		
		if($return_unix)
			return $unix_time;

		return date($to_mask, $unix_time);
	}
}

//$date = '2006-08-04 12:00:02'; // ie. 4 Aug 04 12:00:02pm
//echo Date::convert($date, 'Y-m-d H:i:s', 'Y-m-d H:i:s') . "\n";

//$date = '1801'; // ie. 18 Jan 79 7pm
//echo Date::convert($date, 'dm', '2012-m-d') . "\n";

//$date = '041204 1630'; // ie. 4 Dec 04 4:30pm
//echo Date::convert($date, 'dmy Hi', 'Y-m-d H:i:s') . "\n";

?>
