<?php
    namespace Lib\Form;
    /**
     * [Passwordfield.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Passwordfield extends InputField {

        public function __construct() {
            $this->type = 'password';
        }
    }
?>
