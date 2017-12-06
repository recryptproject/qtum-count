<?php
define('DB', 'qtum');

function getManager() {
	static $m;
	if ($m === null) {
		$m = new  MongoDB\Driver\Manager(MONGO_URL);
	}
	return $m;
}

function insertMongo($table, $id, $data) {
	$m = getManager();
	$bulk = new MongoDB\Driver\BulkWrite;
	$data['_id'] = $id;
	$bulk->update(['_id'=>$id], $data, ['upsert' => true]);
	$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
	$result = $m->executeBulkWrite(DB.'.'.$table, $bulk, $writeConcern);
	return $result;
}

function readMongo($table, $field, $where, $order = null, $offset = null, $limit = null) {
	$filter = $where;
	$options = [];
	if($order !== null) $options['sort'] = $order;
	if($offset !== null) $options['skip'] = $offset;
	if($limit !== null) $options['limit'] = $limit;
	if($field != '*') $options['projection'] = $field;
	$m = getManager();
	$query = new MongoDB\Driver\Query($filter, $options);
	$cursor = $m->executeQuery(DB.'.'.$table, $query);
	$res = [];
	foreach($cursor as $v) {
		$res[] = json_decode(json_encode($v), true);
	}
	return $res;
}

