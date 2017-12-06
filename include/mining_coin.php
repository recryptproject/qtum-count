<?php
require_once(__DIR__.'/header.php');

function miningCoin($beginHeight, $endHeight) {
	$sum = [];
	$totalCoin = $totalCount = 0;
	$maxIn = ['height'=>'', 'amount'=>0];
	$minIn = ['height'=>'', 'amount'=>100000];

	for($step = 500, $pos = 0;;$pos+=$step) {
		printf("\rFetch Next %d Blocks From %d", $step, $pos+$beginHeight);
		$res = readMongo('tx', ['vin', 'blockheight'], ['blockheight' => ['$gte' => intval($beginHeight), '$lte' => intval($endHeight)], 'type' => 1], ['_id' => -1], $pos, $step);
		if(count($res) == 0) break;
		$vinTx = $blockIn = [];
		foreach($res as $v) {
			foreach($v['vin'] as $vin) {
				$vinTx[$vin['txid']][$v['blockheight']][] = $vin['vout'];
				$blockIn[$v['blockheight']] = 0;
			}
		}
		$txList = readMongo('tx', ['vout', 'txid'], ['_id' => ['$in' => array_keys($vinTx)]]);
		foreach($txList as $tx) {
			foreach($tx['vout'] as $vout) {
				foreach($vinTx[$tx['txid']] as $height => $posList) {
					foreach($posList as $vpos) {
						if(isset($vout['n']) && $vout['n'] == $vpos) {
							$blockIn[$height] += $vout['value'];
						}
					}
				}
			}
		}
		foreach($blockIn as $height => $in) {
			if($in > $maxIn['amount']) {
				$maxIn = [
					'height' => $height,
					'amount' => $in
				];
			}
			if($in < $minIn['amount']) {
				$minIn = [
					'height' => $height,
					'amount' => $in
				];
			}
			$totalCoin += $in;
			$totalCount ++;
			$formatIn = floor($in / 100);
			if($formatIn < 20) {
				$key = sprintf("%s - %s", $formatIn * 100, $formatIn * 100 + 100);
			}
			else if ($formatIn < 200) {
				$formatIn = floor($formatIn * 100 / 1000);
				$key = sprintf("%s - %s", $formatIn * 1000, $formatIn * 1000 + 1000);
			}
			else {
				$key = '20000+';
			}
			if(!isset($sum[$key])) $sum[$key] = 0;
			$sum[$key]++;
		}
	}
	ksort($sum, SORT_NUMERIC);
	printf("\r");

	drawChart($sum, 'The coin\'s amount of tx which mined a block from block '.$beginHeight. ' to '.$endHeight.' of QTUM', sprintf("Avarage Coin is %s QTUMs\nThe maximum amount is %s on %s\nThe minimum amount is %s on %s", round($totalCoin/$totalCount, 2), $maxIn['amount'], $maxIn['height'], $minIn['amount'], $minIn['height']), 'QTUMs', 'block(s)');
}
