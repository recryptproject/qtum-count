<?php
require_once(__DIR__.'/header.php');

function req($method, $params) {
	//curl --user myusername --data-binary '{"jsonrpc": "1.0", "id":"curltest", "method": "importwallet", "params": ["test"] }' -H 'content-type: text/plain;' http://127.0.0.1:3889/
	$url = QTUM_URL;
	$data = [
		'jsonrpc' => '1.0',
		'id' => uniqid(),
		'method' => $method,
		'params' => $params
	];
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: text/plain']);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	$res = curl_exec($curl);
	curl_close($curl);
	return json_decode($res, true)['result'];
}

function getBlockByHash($hash) {
	return req('getblock', [$hash]);
}

function getBlockByHeight($height) {
	$id = req('getblockhash', [$height]);
	return getBlockByHash($id);
}

function getRawTx($txid) {
	return req('getrawtransaction', [$txid]);
}

function decodeRawTx($str) {
	return req('decoderawtransaction', [$str]);
}

function getTx($txid) {
	$str = getRawTx($txid);
	return decodeRawTx($str);
}

function getBlockCount() {
	return req('getblockcount', []);
}

function fetch($minHeight, $maxHeight) {
	for($i = $minHeight; $i <= $maxHeight; $i++) {
		printf("\rFetch Block %d (%d/100%%)", $i, round(100*($i-$minHeight)/($maxHeight-$minHeight), 2));

		$block = getBlockByHeight($i);
		insertMongo('block', $block['height'], $block);

		$type = 0;
		foreach($block['tx'] as $txid) {
			$tx = getTx($txid);
			$tx['type'] = $type;
			$tx['blockheight'] = $i;
			if($type < 2) $type ++;
			insertMongo('tx', $txid, $tx);
		}
	}
	echo PHP_EOL;
}

function fetchFrom1() {
	$curHeight = getBlockCount();
	fetch(1, $curHeight);
}

function fetchRecent($number) {
	$curHeight = getBlockCount();
	fetch($curHeight-$number, $curHeight);
}
