<?php
    namespace Lib\Form;
    /**
     * [InputField.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class InputField {
        /**
         * Contains template class
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * Contains field type
         * @var string
         */
        protected $type;
        /**
         * Contains field id
         * @var string
         */
        protected $id;
        /**
         * Contains field name
         * @var string
         */
        protected $name;
        /**
         * contains field value
         * @var string
         */
        protected $value;
        /**
         * marked if field is checked
         * @var boolean
         */
        protected $checked;
        /**
         * marked if field is disabled
         * @var boolean
         */
        protected $disabled;
        /**
         * marked if field is readonly
         * @var boolean
         */
        protected $readonly;
        /**
         * contains maxlength of field
         * @var int
         */
        protected $maxlength;
        /**
         * contains regex for check
         * @var string
         */
        protected $regex;
        /**
         * contains used template
         * @var string
         */
        protected $tpl_name = 'input.tpl';

        /**
         * Initializes class
         * @throws \Core\Mexception
         */
        public function __construct() {
            if (strstr(get_called_class(), 'InputField')) {
                throw new \Core\Mexception('InputField is not allowed to be called directly');
            }
        }

        /**
         * called after clone
         * reset params
         */
        public function __clone() {
            $this->name = null;
            $this->value = null;
        }

        /**
         * Set id attribute
         * @param string $id
         * @return boolean
         */
        public function setId($id) {
            $this->id = $id;
            return true;
        }

        /**
         * get field name
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * set field name
         * @param string $name
         * @return boolean
         */
        public function setName($name) {
            $this->name = $name;
            return true;
        }

        /**
         * set field value
         * @param string $value
         * @return boolean
         */
        public function setValue($value) {
            $this->value = $value;
            return true;
        }

        /**
         * trigger, disable field
         * @return boolean
         */
        public function disable() {
            if ($this->disabled)
                $this->disabled = false;
            else
                $this->disabled = true;
            return true;
        }

        /**
         * trigger, set readonly
         * @return boolean
         */
        public function setReadonly() {
            if ($this->readonly)
                $this->readonly = false;
            else
                $this->readonly = true;
            return true;
        }

        /**
         * set maxlength of field
         * @param int $maxlength
         */
        public function setMaxLength($maxlength) {
            $this->maxlength = (int)$maxlength;
        }

        /**
         * set a regex (for check)
         * @param string $regex
         * @return boolean
         */
        public function setRegex($regex) {
            $this->regex = $regex;
            return true;
        }

        /**
         * validates field on given regex
         * @param string $value
         * @return boolean
         */
        public function validate($value) {
            if (preg_match($this->regex, $value)) {
                return true;
            }
            return false;
        }

        /**
         * fill field with sent value
         * @param string $method
         */
        public function fill($method) {
            if ($method == 'GET') {
                $req = $_GET;
            } else {
                $req = $_POST;
            }
            if (isset($req[$this->name])) {
                $this->value = htmlentities($req[$this->name]);
            }
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . $this->tpl_name);
            $this->Template->assign('id', $this->id);
            $this->Template->assign('type', $this->type);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('value', $this->value);
            $this->Template->assign('checked', $this->checked);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('readonly', $this->readonly);
            $this->Template->assign('maxlength', $this->maxlength);
            $string = preg_replace('/\s{2,}/sm', ' ', $this->Template->parse());
            return $string;
        }
    }
?>
