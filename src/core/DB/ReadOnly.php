<?php

namespace DB;

use config;
use mysqli;

abstract class ReadOnly implements \ArrayAccess
{
    private $container;
    /** @var mysqli */
    protected $dbh;

    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize()
    {
        $config = config::get();
        $this->dbh = new mysqli();
        $this->dbh->real_connect($config['db_readonly_host'], $config['db_readonly_user'], $config['db_readonly_password'], $config['db_schema']);

    }

    /**
     * @param \DB\ID $ID
     * @return false|\mysqli_result|null
     * @throws \Exception
     */
    public function getByID($ID)
    {
        $sql = strtr('SELECT * FROM {table-name} WHERE {id-column} = ? ',
            ['{table-name}' => $this->getTableName(),
                '{id-column}' => $ID->getColumnName()]);
        $stmt = $this->dbh->stmt_init();
        if (!$stmt->prepare($sql)) {
            error_log('error preparing statement: ' . $this->dbh->errno);
        }
        $result = null;
        if ($stmt) {
            try {
                $type = $ID->getType();
                $value = $ID->getValue();
                error_log('type:' . $type . 'value: ' . $value);
                $stmt->bind_param($type, $value);
                if ($stmt->execute()) {

                    $result = $stmt->get_result();
                    $data = $result->fetch_assoc();

                    $this->setContainer($data);
                } else {
                    error_log('execute failed' . $stmt->error);
                }
            } finally {
                $stmt->close();
            }
        } else {
            error_log('Prepare failed' . $this->dbh->error);
        }
        return true;
    }

    protected function getByPredicateSet($sql, PredicateSet $ps)
    {
        $types = $ps->getBindTypes();
        $predSql = $ps->getAsSQL();
        $values = $ps->getValues();
        $result = null;
        $stmt = $this->dbh->prepare($sql);
        if ($stmt) {
            try {
                for ($i = 0; $i < strlen($types); ++$i) {
                    $stmt->bind_param($types{$i}, $values[$i]);
                }
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    $data = $result->fetch_assoc();
                    $this->setContainer($data);
                }
            } finally {
                $stmt->free_result();
            }
        }
        return $result;
    }

    abstract public function getByAPIPredicate(PredicateSet $predicateSet);

    /**
     * @return string
     * @throws \Exception
     */
    public function getTableName()
    {

        $calledclass = get_called_class();
        if (!in_array($calledclass, [ReadOnly::class, ReadWrite::class])) {
            return str_ireplace('DB\\', '', $calledclass);
        }
        throw new \Exception('Invalid Table Name');
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->container);
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function offsetGet($offset)
    {
        return $this->container[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
}