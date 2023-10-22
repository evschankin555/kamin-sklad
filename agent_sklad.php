<?php
$filename = dirname(__FILE__) . '/robots.txt';
if (file_exists($filename))
{
	unlink($filename);
	echo 'Removed';
}
else
{
	echo 'Not exist';
}
