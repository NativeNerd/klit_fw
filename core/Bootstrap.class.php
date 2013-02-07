<?php
    namespace Core;
    /**
     * [Bootstrap.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc
     *
     * previous     now     what changed
     *              1.0.0   -
     *
    */
    class Bootstrap {
        protected $options;
        protected $result;

        public function __construct() {
            $json = file_get_contents('core/bootstrap.json');
            $this->options = json_decode($json);
            $this->result[] = $this->checkPhpVersion();
            $this->result[] = $this->checkNeededFiles();
            $this->result[] = $this->checkDirectories();
        }

        public function getResult() {
            if (in_array(false, $this->result)) {
                return false;
            }
            return true;
        }

        public function checkLive() {

        }

        public function checkPhpVersion() {
            if (version_compare(PHP_VERSION, $this->options->phpversion, 'ge')) {
                return true;
            }
            return false;
        }

        public function checkDirectories() {
            return true;
        }

        public function checkNeededFiles() {
            return true;
        }
    }
?>
