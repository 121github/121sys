<?php 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function contains($str, array $arr)
{
	$found = array();
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) $found[] = $a;
    }
	if(count($found>0)){ return $found; }
    return false;
}
 
 function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
function force_rmdir($dir) {
    foreach(scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) continue;
        if (is_dir("$dir/$file")) force_rmdir("$dir/$file");
        else unlink("$dir/$file");
    }
    rmdir($dir);
}

?>