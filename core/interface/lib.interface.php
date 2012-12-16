<?php
    namespace Core\Implement;
    /**
     * [lib.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    interface lib {
        public function __construct();
        public static function getInstance();
        public function __destruct();
    }

?>
