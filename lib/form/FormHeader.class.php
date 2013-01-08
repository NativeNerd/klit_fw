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
        /**
         * Contains form action
         * @var string
         */
        protected $action;
        /**
         * Contains form method
         * @var string
         */
        protected $method;
        /**
         * Contains hidden field object
         * @var \Lib\Form\InputField
         */
        protected $Hidden;
        /**
         * contains form id
         * @var string
         */
        protected $hash;

        /**
         * sets form action
         * @param string $action
         * @return boolean
         */
        public function setAction($action) {
            $this->action = $action;
            return true;
        }

        /**
         * form uses post
         * @return boolean
         */
        public function usePost() {
            $this->method = 'POST';
            return true;
        }

        /**
         * form uses get
         * @return boolean
         */
        public function useGet() {
            $this->method = 'GET';
            return true;
        }

        /**
         * returns set method
         * @return type
         */
        public function getMethod() {
            return $this->method;
        }

        /**
         * sets form id (hash)
         * @param string $hash
         * @return boolean
         */
        public function setHash($hash) {
            $this->Hidden = new Hidden();
            $this->Hidden->setName('formId');
            $this->Hidden->setValue($hash);
            $this->hash = $hash;
            return true;
        }

        /**
         * Parses to string
         * @return string
         */
        public function __toString() {
            return '<form method="'.$this->method.'" action="'.$this->action.'" name="'.$this->hash.'">'
                . (string)$this->Hidden;
        }
    }

?>
