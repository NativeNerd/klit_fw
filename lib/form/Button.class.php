<?php
    namespace Lib\Form;
    /**
     * [Button.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Button extends InputField {

        public function __construct() {
            $this->type = 'submit';
        }
    }

?>
