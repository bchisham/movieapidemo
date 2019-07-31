<?php

require_once realpath('../../../../core/bootstrap.php');

$api = new \API\Performers();
$api->processRequest();
$api->output();