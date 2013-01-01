<?php
    namespace Lib\Path;
    /**
     * [Path.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Build path and uri
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Path implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        protected $param;

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

        // adds a param to the uri
        public function addParam($key, $value) {
            $this->param[$key] = $value;
            return true;

        }

        // returns back the GET-string
        public function buildGet($seo = false) {
            if (is_array($this->param)) {
                if ($seo) {
                    $glue = '/';
                    $alloc = ':';
                } else {
                    $glue = '&';
                    $alloc = '=';
                }
                $uri = array();
                foreach ($this->param AS $key=>$value) {
                    $key = urlencode($key);
                    $value = urlencode($value);
                    $uri[] = $key . $alloc . $value;
                }
                return implode($glue, $uri);
            } else {
                return '';
            }
        }

        // Builds an href compatible string
        // starts with index.php
        public function href() {
            $get = $this->buildUri();
            return 'index.php?'.$get;
        }

        // Helper::parseUri()
        public function parseUri() {
            if (@strlen($_SERVER['QUERY_STRING']) > 0) {
                $str = explode('&', $_SERVER['QUERY_STRING']);
                $newKey = array();
                $newValue = array();
                foreach ($str AS $value) {
                    if (substr($value, 0, 1) == '/') {
                        $value = substr($value, 1);
                    }
                    $value = explode('=', $value);
                    $newKey[] = urldecode($value[0]);
                    $newValue[] = urldecode($value[1]);
                }
                return array_combine($newKey, $newValue);
            } elseif (@strlen($_SERVER['PATH_INFO']) > 0) {
                $str = explode('/', $_SERVER['PATH_INFO']);
                $newKey = array();
                $newValue = array();
                unset($str[0]);
                $newKey[] = 'main'; $newValue[] = $str[1];
                $newKey[] = 'action'; $newValue[] = $str[2];
                unset($str[1]); unset($str[2]);
                foreach ($str AS $value) {
                   $tmp = explode(':', $value, 2);
                   $newKey[] = urldecode($tmp[0]);
                   $newValue[] = urldecode($tmp[1]);
                   unset($tmp);
                }
                return array_combine($newKey, $newValue);
            } else {
                return false;
            }
        }

        // Helper::buildPath()
        public function buildPath($pathOrFile, $throwError = true) {
            if (strpos($pathOrFile, '../')) {
                $pathOrFile = str_replace('../', null, $pathOrFile);
                return $this->buildPath($pathOrFile, $throwError);
            }
            $wholePath = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen(basename($_SERVER['SCRIPT_FILENAME'])))
                .$pathOrFile;
            if (is_dir($wholePath))
                return $wholePath .= '/';
            elseif (is_file($wholePath))
                return $wholePath;
            elseif ($throwError)
                throw new \Core\Mexception('Invalid path given, given ' . $pathOrFile);
            else
                return $wholePath;
        }

        public function __destruct() {

        }

    }

?>
