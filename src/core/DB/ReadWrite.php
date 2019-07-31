<?php

namespace DB;

use config;

abstract class ReadWrite extends ReadOnly
{

    protected function initialize()
    {
        $config = config::get();
        $this->dbh = new \mysqli();
        $this->dbh->real_connect($config['db_write_host'], $config['db_write_user'], $config['db_write_password'], $config['db_schema']);
    }
}