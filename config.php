<?php
require 'Medoo.php';
use Medoo\Medoo;
$database = new Medoo([
    	'type' => 'sqlite',
    	'database' => '/var/www/html/database.db'
]);