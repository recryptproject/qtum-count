<?php
function drawChart($data, $header = '', $footer = '', $kUnit = '', $vUnit = '') {
	$chips = 100;
	$maxData = 0;
	$longestKey = 0;
	foreach($data as $k => $v) {
		$maxData = max($v, $maxData);
		$longestKey = max(strlen($k.' '.$kUnit), $longestKey);
	}
	$width = $longestKey + 3 + $chips + 1 + strlen($maxData) + strlen($vUnit) + 1;

	printf("%s\n", str_pad('', $width, '-'));
	printf("%s\n", str_pad($header, $width, ' ', STR_PAD_BOTH));
	printf("%s\n", str_pad('', $width, '-'));
	foreach($data as $k => $v) {
		$progress = str_pad('', round($chips * $v/$maxData), '#');
		printf("%s | %s %s\n", str_pad($k.' '.$kUnit, $longestKey), $progress, $v>0?$v.' '.$vUnit:'');
	}
	printf("%s\n", str_pad('', $width, '-'));
	if($footer) {
		if(strpos($footer, "\n") !== false) {
			$footer = explode("\n", $footer);
		}
		else {
			$footer = [$footer];
		}
		foreach($footer as $line) {
			printf("%s\n", str_pad($line, $width, ' ', STR_PAD_LEFT));
		}
		printf("%s\n", str_pad('', $width, '-'));
	}
	echo "\n";
}
