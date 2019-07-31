<?php

class config implements ArrayAccess
{
    private static $inst;
    private $container;

    static public function get()
    {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    public function __get($name)
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }
        return null;
    }

    private function __construct()
    {
        $this->container = [];
        $dirp = opendir(self::getINIPath());
        if (!is_resource($dirp)) {
            return;
        }
        while (false !== ($file = readdir($dirp))) {
            if (preg_match('#\.ini$#', $file)) {
                $this->loadFile(APP_DIR_INI . DIRECTORY_SEPARATOR . $file);
            }
        }

    }

    private function loadFile($file)
    {
        $lines = explode(PHP_EOL, file_get_contents($file));
        foreach ($lines as $line) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $this->container[$key] = $value;
            }

        }
    }

    static private function getINIPath()
    {
        return APP_DIR_INI;
    }

    public function offsetGet($offset)
    {
        if (isset($offset)) {
            return $this->container[$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->container);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

}