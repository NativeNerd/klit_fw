<?php
    namespace Lib\Form;
    /**
     * [Checkbox.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Checkbox extends InputField {

        public function __construct() {
            $this->type = 'checkbox';
        }

        /**
         * Marks checkbox as checked
         * trigger function
         */
        public function check() {
            if ($this->checked)
                $this->checked = false;
            else
                $this->checked = \Config\Form::VALUE_CHECKED;
        }
    }
?>
