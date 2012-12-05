<?php
    /**
     * [index.controller.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Was it does
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    namespace Src\Controller;
    class index {
        private $Bootstrap;

        public function __construct(\Core\Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
        }

        public function show($uri) {
            echo 'show';
        }

        public function __desctruct() {

        }

    }

?>
