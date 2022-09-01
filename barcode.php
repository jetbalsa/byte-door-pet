<?php
require 'config.php';
// See if the barcode already exists
$hash = md5($_POST['data']);
if(!$database->has("barcode", ['idhash' => $hash])){
	$database->insert("barcode", [
    	"idhash" => $hash,
    	"data" => $_POST['data'],
    	"firstseen" => time(),
    	"lastseen" => time()
    ]);
$new = 1;
}else{
$new = 0;
$data = $database->update("barcode", [
	"lastseen" => time()
], [
	"idhash" => $hash
]);
}
//datapost = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['data']);
//$lookup = file_get_contents("https://api.upcdatabase.org/product/$datapost?apikey=79FFE07B07FB65EF4B26680001D0CF84");
$data = $database->update("state", [
	"data" => json_encode(['data' => $_POST['data'], 'hash' => $hash, 'new' => $new]),
	"time" => time()
], [
	"name" => "lastbarcode"
]);
