<?php

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
    namespace Core\Implement;
    interface controller {
        public function __construct(\Core\Bootstrap $Bootstrap);
        public function __desctruct();
    }

?>
