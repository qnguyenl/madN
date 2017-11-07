<?php
/**
 * Created by PhpStorm.
 * User: quynguyenlam
 * Date: 09.04.17
 * Time: 18:35
 */

$config = json_decode(file_get_contents('config.json'),true);

App::bind("config",$config);
