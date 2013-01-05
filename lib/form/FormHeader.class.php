<?php
    namespace Lib\Form;
    /**
     * [FormHeader.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class FormHeader {
        protected $action;
        protected $method;
        protected $Hidden;
        protected $hash;

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

        public function setHash($hash) {
            $this->Hidden = new Hidden();
            $this->Hidden->setName('formId');
            $this->Hidden->setValue($hash);
            $this->hash = $hash;
            return true;
        }

        public function __toString() {
            return '<form method="'.$this->method.'" action="'.$this->action.'" name="'.$this->hash.'">'
                . (string)$this->Hidden;
        }
    }

?>
