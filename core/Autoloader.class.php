<?php
    namespace Core;
    /**
     * [Autoloader.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Autoloader {

        public function __construct() {

        }

        public static function buildPath($file) {
            if (strpos($file, '../')) {
                $file = str_replace('../', null, $file);
                return $this->buildPath($file);
            }
            $wholePath = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen(basename($_SERVER['SCRIPT_FILENAME'])))
                .$file;

            if (is_file($wholePath))
                return $wholePath;
            else
                return false;
        }

        public static function load($className) {
            $tmp = explode('\\', $className);

            

            if (($path = Autoloader::buildPath($classFileCore)) !== false) {
                require_once Autoloader::buildPath($classFileCore);
            } elseif (($path = Autoloader::buildPath($classFileLib)) !== false) {
                require_once Autoloader::buildPath($classFileLib);
            }
            if (($pathConfig = Autoloader::buildPath($classConfig)) !== false) {
                require_once Autoloader::buildPath($classConfig);
            }
            return ;
        }

        public function __desctruct() {

        }

    }

?>
