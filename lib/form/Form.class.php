<?php
    namespace Lib\Form;
    /**
     * [Form.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Form implements \Core\Interfaces\lib {
        protected $id;
        protected $InputFields;
        protected $Selects;
        protected $Labels;
        protected $Header;
        protected $Footer;
        protected $method;
        protected $action;
        protected $Own;

        public function __construct() {
            $this->Header = new FormHeader();
            $this->Own[':header'] = 'Header';
            $this->Footer = new FormFooter();
            $this->Own[':footer'] = 'Footer';
            return ;
        }

        protected function buildHash($id) {
            return substr(sha1($id), 0, 6);
        }


        public function setId($id) {
            $this->id = $this->buildHash($id);
            $this->Header->setHash($this->id);
            return true;
        }

        public function getId() {
            return $this->id;
        }

        public function save() {
            return ;
        }

        public function check() {
            return ;
        }

        public function fill() {
            foreach ($this->Selects AS $Select) {
                $Select->fill($this->Header->getMethod());
            }
            foreach ($this->InputFields AS $InputField) {
                $InputField->fill($this->Header->getMethod());
            }
            return true;
        }

        public function setAction($action) {
            $this->Header->setAction($action);
            return true;
        }

        public function usePost() {
            $this->Header->usePost();
            return true;
        }

        public function useGet() {
            $this->Header->useGet();
            return true;
        }

        public function registerInput($Input) {
            if (!is_object($Input)) {
                return false;
            }
            $extends = class_parents($Input);
            if (!in_array('Lib\Form\InputField', $extends)) {
                return false;
            }
            $this->InputFields[$Input->getName()] = $Input;
            return true;
        }

        public function registerSelect(Select $Select) {
            $this->Selects[$Select->getName()] = $Select;
            return true;
        }

        public function registerLabel(Label $Label) {
            $this->Labels[$Label->getAssignedTo()] = $Label;
            return true;
        }

        public function getItem($name) {
            if (isset($this->Selects[$name])) {
                return (string)$this->Selects[$name];
            }
            if (isset($this->InputFields[$name])) {
                return (string)$this->InputFields[$name];
            }
            if ($name == ':header') {
                return (string)$this->Header;
            }
            if ($name == ':footer') {
                return (string)$this->Footer;
            }
            return false;
        }

        public function getLabel($name) {
            if (isset($this->Labels[$name])) {
                return (string)$this->Labels[$name];
            }
            return false;
        }

        public function __destruct() {
            return ;
        }
    }
?>
