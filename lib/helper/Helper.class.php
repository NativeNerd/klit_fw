<?php
    namespace Lib\Helper;
    /**
     * [Helper.class.php]
     * @version 1.1.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Some helping functions
     *
     * previous     now     what changed
     *              1.0.0   -
     * 1.0.0        1.1.0   buildPath() neu ohne $character, gibt nun Fehler bei inexistentem Pfad (als default)
     *              1.1.0   buildPath() now without $path (now $_unused)
     *
     */
    class Helper implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;

        public function __construct() {

        }

        public static function getInstance(\Core\Bootstrap $Bootstrap = null) {
            if ($Bootstrap !== null) {
                static::$Bootstrap = $Bootstrap;
            }
            if (static::$_instance === null) {
                static::$_instance = new static();
            }
            return static::$_instance;
        }

        public function __destruct() {

        }

        /**
         * Builds an absolute path
         * @param string $path
         * @param null $_unused
         * @param null $_unused2
         * @param boolean $error
         * @return string|boolean
         * @throws \Core\Mexception
         * @deprecated Path::buildPath()
         */
        final static public function buildPath($path, $_unused = null, $_unused2 = null, $error = true) {
            if (substr($path, 0, 2) == '..') {
                $path = substr($path, 3);
                return \Lib\Helper::buildPath($path, null, $extension, $error);
            }
            // Project directory
            $dir = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen(basename($_SERVER['SCRIPT_FILENAME'])))
                .$path;

            if (is_dir($dir))
                return $dir .= '/';
            elseif (is_file($dir))
                return $dir;
            elseif ($error)
                throw new \Core\Mexception('Invalid path given, given '.$path);
            else
                return false;
        }

        /**
         * Builds a database compatible date of a given timestamp
         * @param int $timestamp
         * @return string
         */
        final static public function buildDate($timestamp = null) {
            if ($timestamp == null)
                $timestamp = time();
            return date('Y-m-d', $timestamp);
        }

        /**
         * Builds a database compatible datetime of a given timestamp
         * @param int $timestamp
         * @return string
         */
        final static public function buildDatetime($timestamp = null) {
            if ($timestamp == null)
                $timestamp = time();
            return date('Y-m-d H:i:s', $timestamp);
        }

        /**
         * Containts the user ip adress
         * @return string
         */
        final static public function getIp() {
            return $_SERVER['REMOTE_ADDR'];
        }

        /**
         * Parses an URI
         * @return array|boolean
         * @deprecated Path::parseUri()
         */
        final static public function parseUri() {
            if (strlen($_SERVER['QUERY_STRING']) > 0) {
                $str = explode('&', $_SERVER['QUERY_STRING']);
                $newKey = array();
                $newValue = array();
                foreach ($str AS $value) {
                    if (substr($value, 0, 1) == '/') {
                        $value = substr($value, 1);
                    }
                    $value = explode('=', $value);
                    $newKey[] = $value[0];
                    $newValue[] = $value[1];
                }
                return array_combine($newKey, $newValue);
            } elseif (strlen($_SERVER['PATH_INFO']) > 0) {
                $str = explode('/', $_SERVER['PATH_INFO']);
                $newKey = array();
                $newValue = array();
                unset($str[0]);
                $newKey[] = 'main'; $newValue[] = $str[1];
                $newKey[] = 'action'; $newValue[] = $str[2];
                unset($str[1]); unset($str[2]);
                foreach ($str AS $value) {
                   $tmp = explode(':', $value, 2);
                   $newKey[] = $tmp[0];
                   $newValue[] = $tmp[1];
                   unset($tmp);
                }
                return array_combine($newKey, $newValue);
            } else {
                return false;
            }
        }

        /**
         * Notates $value like a given type, database compatible
         * @deprecated Query::_notationByType()
         * @param mixed $value
         * @param string $type
         * @return null|string
         */
        final static public function db_notationByType($value, $type) {
            $matches = array();
            if (!preg_match('$([a-zA-Z]+)\(([0-9]+)\)$', $type, $matches)) {
                if (!preg_match('$([a-zA-Z]+)$', $type, $matches)) {
                    return null;
                }
            }
            switch (strtolower($matches[1])) {
                case 'varchar' :
                    return '"'.$value.'"';
                case 'int' :
                    return $value;
                case 'float' :
                    return $value;
                case 'bool' :
                    if ($value == 1 OR $value == true) {
                        return 'TRUE';
                    } else {
                        return 'FALSE';
                    }
                case 'decimal' :
                    return $value;
                case 'datetime' :
                    return '"'.$value.'"';
                default :
                    return null;
            }
        }
    }
?>