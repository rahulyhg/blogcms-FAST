<?php
	include_once dirname(__FILE__)."/../configs.php";
	$json = json_decode( $_POST['json'], true );
	$id = $json["id"];
	$db = MongoConnection();
	$db_getter = new MongoGetter( $db );	
	$data = $db_getter->getSingleRowById( $id );
	$ret = json_encode( $data );
	echo $ret;

?>