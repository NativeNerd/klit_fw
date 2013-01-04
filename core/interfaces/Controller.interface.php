<?php
    namespace Core\Interfaces;
    /**
     * [controller.interface.php]
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
    interface controller {
        public function __construct();
        public function _before($uri);
        public function _after($uri);
        public function _fallback($uri);
        public function __desctruct();
    }

?>
