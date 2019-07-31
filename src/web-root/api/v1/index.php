<?php

require_once realpath('../../../core/bootstrap.php');
$v1 = new \API\v1();
$v1->processRequest();
$v1->output();
