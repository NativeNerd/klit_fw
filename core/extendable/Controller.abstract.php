<?php
    namespace Core\extendable;
    /**
     * [Controller.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Controller {
        public function __call($name, $argv) {
            $this->__fallback();
            return ;
        }
    }
?>
