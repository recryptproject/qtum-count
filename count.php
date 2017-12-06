<?php
if(isset($argv[1])) {
	switch($argv[1]) {
		case 'init': 
			include(__DIR__.'/include/fetch.php');
			fetchFrom1();
			break;
		case 'sync':
			include(__DIR__.'/include/fetch.php');
			if(isset($argv[2]) && preg_match('/^[1-9][0-9]*$/', $argv[2])) $count = $argv[2];
			else $count = 50;
			fetchRecent($count);
			break;
		case 'span':
			if(isset($argv[2]) && isset($argv[3]) && preg_match('/^[1-9][0-9]*$/', $argv[2]) && preg_match('/^[1-9][0-9]*$/', $argv[3]) && $argv[3] > $argv[2]) {
				include(__DIR__.'/include/span.php');
				span($argv[2], $argv[3]);
			}
			else {
				printHelp();
			}
			break;
		case 'miningcoin':
			if(isset($argv[2]) && isset($argv[3]) && preg_match('/^[1-9][0-9]*$/', $argv[2]) && preg_match('/^[1-9][0-9]*$/', $argv[3]) && $argv[3] > $argv[2]) {
				include(__DIR__.'/include/mining_coin.php');
				miningCoin($argv[2], $argv[3]);
			}
			else {
				printHelp();
			}
			break;
		default:
			printHelp();
	}
}
else {
	printHelp();
}


function printHelp(){
    echo "Usage:".PHP_EOL;
    echo "sync all blocks to mongodb: php count.php".PHP_EOL;
    echo "sync recent blocks to mongodb (used at crontab): php count.php sync (50)".PHP_EOL;
    echo "cal the block time span: php count.php span 20000 30000".PHP_EOL;
    echo "cal the coins' amount which mined a block: php count.php miningcoin 20000 30000".PHP_EOL;
}
