<?php
require_once(__DIR__.'/header.php');

function span($beginHeight, $endHeight) {
	$data = [];
	for($step = 100, $pos = 0;;$pos+=$step) {
		$res = readMongo('block', '*', ['_id' => ['$gte' => intval($beginHeight), '$lte' => intval($endHeight)]], ['_id' => -1], $pos, $step);
		if(count($res) == 0) break;
		foreach($res as $v) {
			$data[$v['_id']] = $v['time'];
		}
	}
	$sum = [];
	$more = 0;
	$maxKey = 0;
	$totalSpan = 0;
	$totalCount = 0;
	foreach($data as $height => $time) {
		if(!isset($data[$height-1])) continue;
		$key = $time - $data[$height-1];
		$totalSpan += $key;
		$totalCount ++;
		if($key > 720) {
			$more ++;
			continue;
		}
		if(!isset($sum[$key])) {
			$sum[$key] = 0;
		}
		$sum[$key]++;
		$maxKey = max($maxKey, $key);
	}

	$sortSum = [];

	for($i = 1; $i <= $maxKey; $i++) {
		if(isset($sum[$i])) $sortSum[$i] = $sum[$i];
	}
	$sortSum['More than 720'] = $more;

	drawChart($sortSum, 'The time span summary of blocks from block '.$beginHeight. ' to '.$endHeight.' of RECRYPT', 'Avarage Span is '.round($totalSpan/$totalCount, 2).' sec', 'sec', 'block(s)');
}
