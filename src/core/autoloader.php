<?php


final class autoloader
{


    static private $inst;

    private $pathList;

    /**
     * Initialize the autoloader if it hasn't been already and add the specified path to the search path.
     * @param $path
     */
    public static function quickPath($path)
    {
        static $quickPathsLoaded = [];
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }
        $path = realpath($path);
        if (isset($quickPathsLoaded[$path])) {
            return;
        }
        $dirs = explode(DIRECTORY_SEPARATOR, $path);
        if ($dirs[0] != '') {
            $tail = array_pop($dirs);
        }
        if (in_array(APP_DIR_CORE, $dirs)) {
            do {
                $src = implode(DIRECTORY_SEPARATOR, $dirs);
                self::$inst->addPath($src);
                $tail = array_pop($dirs);
            } while ($tail != self::APP_DIR_CORE);
        } else {
            self::$inst->addPath($path);
        }
        $quickPathsLoaded[$path] = true;
    }

    private function getPath($className)
    {
        if ($className !== 'parent') {
            $classFileName = str_replace('\\', '/', $className) . '.php';
            if (false !== ($classFilePath = stream_resolve_include_path($classFileName))) {
                return $classFilePath;
            } else {
                $parts = pathinfo($classFileName);
                $classFileName = strtolower($classFileName);
                if (false !== ($classFilePath = stream_resolve_include_path($classFileName))) {
                    return $classFilePath;
                }
            }
        }
        return null;
    }

    private function addPath($path)
    {
        if (!in_array($path, $this->pathList)) {
            array_push($this->pathList, $path);
        }
        set_include_path(implode(PATH_SEPARATOR, $this->pathList));
    }


    private function loader($className)
    {
        if (null !== ($expectedPath = $this->getPath($className))) {
            include_once $expectedPath;
        }
    }

    private function __construct()
    {
        $this->pathList = explode(':', get_include_path());
        spl_autoload_register([$this, 'loader']);
    }
}