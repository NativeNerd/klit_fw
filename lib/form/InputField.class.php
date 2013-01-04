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
        protected $Template;

        protected $type;
        protected $id;
        protected $name;
        protected $value;
        protected $checked;
        protected $disabled;
        protected $readonly;
        protected $maxlength;
        protected $regex;

        public function __construct() {
            if (strstr(get_called_class(), 'InputField')) {
                throw new \Core\Mexception('InputField is not allowed to be called directly');
            }
        }

        public function setId($id) {
            $this->id = $id;
            return true;
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
            return true;
        }

        public function setValue($value) {
            $this->value = $value;
            return true;
        }

        public function disable() {
            if ($this->disabled)
                $this->disabled = false;
            else
                $this->disabled = true;
            return true;
        }

        public function setReadonly() {
            if ($this->readonly)
                $this->readonly = false;
            else
                $this->readonly = true;
            return true;
        }

        public function setMaxLength($maxlength) {
            $this->maxlength = (int)$maxlength;
        }

        public function setRegex($regex) {
            $this->regex = $regex;
            return true;
        }

        public function validate($value) {
            if (preg_match($this->regex, $value)) {
                return true;
            }
            return false;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'input.tpl');
            $this->Template->assign('id', $this->id);
            $this->Template->assign('type', $this->type);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('value', $this->value);
            $this->Template->assign('checked', $this->checked);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('readonly', $this->readonly);
            $this->Template->assign('maxlength', $this->maxlength);
            $string =  $this->Template->parse();
            $string = preg_replace('/\s{2,}/sm', ' ', $string);
            return $string;
        }
    }
?>
