<?php
class Log {
	static $mtime = 0;
}

function beginTimeStamp() {
	Log::$mtime = microtime(true);
}

function timeStamp($tag) {
	$prev = Log::$mtime;
	$curr = microtime(true);
	printf("%s spent %f sec \n", $tag, $curr-$prev);
	Log::$mtime = $curr;
}
