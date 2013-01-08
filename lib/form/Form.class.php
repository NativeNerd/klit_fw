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
        /**
         * Containts FormId
         * @var string
         */
        protected $id;
        /**
         * Contains InputFields-Objects
         * @var array
         */
        protected $InputFields;
        /**
         * Contains Select-Objects
         * @var array
         */
        protected $Selects;
        /**
         * Contains Label-Objects
         * @var array
         */
        protected $Labels;
        /**
         * Contains Header-Object
         * @var \Lib\Form\Header
         */
        protected $Header;
        /**
         * Contains Footer-Object
         * @var \Lib\Form\Footer
         */
        protected $Footer;
        /**
         * Contains the Form-Method
         * @var string
         */
        protected $method;
        /**
         * Contains the Form-Action
         * @var string
         */
        protected $action;
        /**
         * Contains internal variables
         * @var array
         */
        protected $Own;

        /**
         * Initializes class
         * @return null
         */
        public function __construct() {
            $this->Header = new FormHeader();
            $this->Own[':header'] = 'Header';
            $this->Footer = new FormFooter();
            $this->Own[':footer'] = 'Footer';
            return ;
        }

        /**
         * Builds a unique hash
         * @param string $id
         * @return string
         */
        protected function buildHash($id) {
            return substr(sha1($id), 0, 6);
        }

        /**
         * Sets the form id
         * @param string $id
         * @return boolean
         */
        public function setId($id) {
            $this->id = $this->buildHash($id);
            $this->Header->setHash($this->id);
            return true;
        }

        /**
         * Gets the form id
         * @return string
         */
        public function getId() {
            return $this->id;
        }

        /**
         * Saves form into session
         * @return boolean
         */
        public function save() {
            return ;
        }

        /**
         * Checks the sent values
         * @return array
         */
        public function check() {
            return ;
        }

        /**
         * Fills the fields with sent values
         * @return boolean
         */
        public function fill() {
            foreach ($this->Selects AS $Select) {
                $Select->fill($this->Header->getMethod());
            }
            foreach ($this->InputFields AS $InputField) {
                $InputField->fill($this->Header->getMethod());
            }
            return true;
        }

        /**
         * Sets the Form-Action
         * @param string $action
         * @return boolean
         */
        public function setAction($action) {
            $this->Header->setAction($action);
            return true;
        }

        /**
         * Form uses post-method
         * @return boolean
         */
        public function usePost() {
            $this->Header->usePost();
            return true;
        }

        /**
         * form uses get-method
         * @return boolean
         */
        public function useGet() {
            $this->Header->useGet();
            return true;
        }

        /**
         * Registers an input field
         * @param \Lin\Form\InputField $Input
         * @return boolean
         */
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

        /**
         * Registers a select
         * @param \Lib\Form\Select $Select
         * @return boolean
         */
        public function registerSelect(Select $Select) {
            $this->Selects[$Select->getName()] = $Select;
            return true;
        }

        /**
         * Registers a label
         * @param \Lib\Form\Label $Label
         * @return boolean
         */
        public function registerLabel(Label $Label) {
            $this->Labels[$Label->getAssignedTo()] = $Label;
            return true;
        }

        /**
         * gets the html of an item
         * @param string $name
         * @return string|boolean
         */
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

        /**
         * gets the html of a label
         * @param string $name
         * @return string|boolean
         */
        public function getLabel($name) {
            if (isset($this->Labels[$name])) {
                return (string)$this->Labels[$name];
            }
            return false;
        }

        /**
         * closes the class
         * @return null
         */
        public function __destruct() {
            return ;
        }
    }
?>
