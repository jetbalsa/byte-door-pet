<?php
require 'config.php';
$data = $database->update("state", [
	"data" => json_encode($_POST),
	"time" => time()
], [
	"name" => "people"
]);