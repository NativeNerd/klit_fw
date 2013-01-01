<?php
    namespace Core;
    /**
     * [Bootstrap.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Handles Objects of the Application
     *
     * previous     now     what changed
     *              1.0.0   -
     *
    */
    class Bootstrap {
        protected $registered;

        /**
         * Registers an application, if application will be called, instance will be opened automatically
         *
         * @param string $applicationName
         * @param string $namespace
         */
        public function registerApplication($applicationName, $className, $namespace) {
            $fullClassName = $namespace.'\\'.$className;
            if (class_exists($fullClassName, false)) {
                $this->registered[$applicationName] = $namespace.'\\'.$className;
                return true;
            } else {
                $path = \Lib\Helper\Helper::buildPath('lib/'.$applicationName.'/'.$className.'.class.php');
                if (file_exists($path)) {
                    require_once $path;
                }
                if (class_exists($fullClassName, false)) {
                    $this->registered[$applicationName] = $namespace.'\\'.$className;
                    return true;
                }
                return false;
            }
        }

        /**
         * Looks wheter an application is registered
         *
         * @param string $applicationName
         * @return boolean
         */
        public function isRegistered($applicationName) {
            if (isset($this->registered[$applicationName])) {
                return true;
            }
            return false;
        }

        /**
         * Creates a new instance of the given application
         *
         * @param string $applicationName
         * @return boolean
         */
        public function openInstance($applicationName) {
            if ($this->isRegistered($applicationName)) {
                $fullClassName = $this->registered[$applicationName];
                $this->$applicationName = new $fullClassName($this);
                if (is_object($this->$applicationName)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        /**
         * Opens a new application in the bootstrap
         * @param object $applicationObject
         * @param string $applicationName
         * @return boolean
         */
        public function openApplication(&$applicationObject, $applicationName) {
            if (is_object($applicationObject)) {
                $this->$applicationName = $applicationObject;
                return true;
            } else {
                return false;
            }
        }

        /**
         * Closes the application in the bootstrap
         * @param string $applicationName
         * @return boolean
         */
        public function closeApplication($applicationName) {
            unset($this->$applicationName);
            return true;
        }

        /**
         * Gets the application object
         * @param string $applicationName
         * @return (object|boolean)
         */
        public function getApplication($applicationName) {
            if (isset($this->$applicationName)) {
                return $this->$applicationName;
            }
            if ($this->isRegistered($applicationName)) {
                if ($this->openInstance($applicationName)) {
                    return $this->$applicationName;
                }
            }
            return false;
        }
    }
?>
