<?php
require_once realpath('../../../../core/bootstrap.php');
$api = new \API\Movies();
$api->processRequest();
$api->output();
