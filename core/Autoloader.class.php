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
        public static function bootstrap() {
            if (class_exists('\Config\Constant', false)) {
                return true;
            }
            exit ;
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

        public static function isFile($file) {
            if (file_exists($file)) {
                return true;
            } else {
                return false;
            }
        }

        public static function load($className) {
            self::bootstrap();
            $stack = explode('\\', $className);
            $file = '';
            $requireConfig = false;
            $requireMap = false;
            foreach ($stack AS $key=>$value) {
                (strlen($file)) ? $file .= '/' : null;
                ((count($stack) - 1) == $key) ? $value = ucfirst($value) : $value = strtolower($value);
                $file .= $value;
            }
            if (strtolower($stack[0]) == 'src') {
                if (strtolower($stack[0]) == 'controller') {
                    $ext = \Config\Constant::FILE_CONTROLLEREXT;
                    $requireConfig = true;
                }
            }
            if (strtolower($stack[0]) == 'core') {
                if (strtolower($stack[1]) == 'interfaces') {
                    $ext = \Config\Constant::FILE_IMPLEMENTEXT;
                } elseif (strtolower($stack[1]) == 'extendable') {
                    $ext = \Config\Constant::FILE_ABSTRACTEXT;
                } else {
                    $ext = \Config\Constant::FILE_COREEXT;
                }
            }
            if (!isset($ext)) {
                $ext = '.php';
            }
            if (strtolower($stack[0]) == 'model') {
                $requireMap = true;
                $ext = \Config\Constant::FILE_MODELEXT;
            }
            if (strtolower($stack[0]) == 'lib') {
                $ext = \Config\Constant::FILE_LIBEXT;
            }
            if (strtolower($stack[0]) == 'config') {
                $ext = \Config\Constant::FILE_CONFIGEXT;
            }
            if ($requireConfig) {
                $config = max($stack);
                $config = $stack[$config];
                $configPath = self::buildPath(\Config\Constant::PATH_CONFIG . $config . \Config\Constant::FILE_CONFIGEXT);
                if ($configPath AND self::isFile($configPath)) require_once $configPath;
            }
            if ($requireMap) {
                $config = max($stack);
                $config = $stack[$config];
                $mapPath = self::buildPath(\Config\Constant::PATH_MAP . $config . \Config\Constant::FILE_MAPEXT);
                if ($mapPath AND self::isFile($mapPath)) require_once $mapPath;
            }
            $filePath = self::buildPath($file . $ext);
            if (self::isFile($filePath)) require_once $filePath;
            return ;
        }
    }

?>
