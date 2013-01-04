<?php
    namespace Lib\Form;
    /**
     * [FormHeader.class.php]
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
     * @todo Any todos?
     */
    class FormHeader {
        protected $action;
        protected $method;

        public function setAction($action) {
            $this->action = $action;
            return true;
        }

        public function usePost() {
            $this->method = 'POST';
            return true;
        }

        public function useGet() {
            $this->method = 'GET';
            return true;
        }

        public function __toString() {
            return '<form method="'.$this->method.'" action="'.$this->action.'">';
        }
    }

?>
