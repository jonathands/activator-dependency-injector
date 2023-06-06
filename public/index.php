<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
$app = require "../src/app.php";

$app->run();

$our10users = $app->db->query("SELECT * FROM cnae LIMIT 0,10")->fetchAll();
var_dump($our10users);