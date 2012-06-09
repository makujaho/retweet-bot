<?php

/**
 * This file is the index file and will display you the log generated from bot.php
 * If this runs not on a local web server you should protect this file via some 
 * kind of access control, e.g. htpasswd
 */

require 'log/PHPTail.php';
require 'config.php';

/**
 * Initilize a new instance of PHPTail
 * @var PHPTail
 */

$tail = new PHPTail(BotConfig::LOG_FILE_PATH);

if(isset($_GET['ajax']))  {
	echo $tail->getNewLines($_GET['lastsize'], $_GET['grep'], $_GET['invert']);
	die();
}

$tail->generateGUI();