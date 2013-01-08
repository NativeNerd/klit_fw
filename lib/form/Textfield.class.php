<?php
    namespace Lib\Form;
    /**
     * [Textfield.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Textfield extends InputField {
        public function __construct() {
            $this->type = 'text';
        }
    }
?>
