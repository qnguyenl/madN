<?php
/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 18:39
 */
require "vendor/autoload.php";
require "boostrap.php";
$config = App::get('config');
try{
	$brett = new Brett($config);
	$zugLogs = $brett ->start();
	$winner = $brett->getWinner();
	$spielers = $brett->getAllSpieler();
}catch(Exception $e){
	$error = $e->getMessage();
}
require "view/index.view.php";