<?php
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
            if (is_object($this->$applicationName)) {
                return $this->$applicationName;
            } else {
                return false;
            }
        }
    }
?>