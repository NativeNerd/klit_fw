<?php
    namespace Lib\Form;
    /**
     * [Option.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Option {
        protected $Template;
        protected $id;
        protected $selected;
        protected $disabled;
        protected $label;
        protected $name;
        protected $value;

        public function __construct($name = null, $value = null) {
            $this->name = $name;
            $this->value = $value;
        }

        public function __clone() {
            $this->name = null;
            $this->value = null;
            $this->label = null;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function assignToGroup(OptionGroup $group) {
            $group->registerOption($this);
            return true;
        }

        public function setSelected($forceFalse = false) {
            if ($forceFalse) {
                $this->selected = false;
                return true;
            }
            if ($this->selected)
                $this->selected = false;
            else
                $this->selected = true;
            return true;
        }

        public function disable() {
            if ($this->disabled)
                $this->disabled = false;
            else
                $this->disabled = true;
            return true;
        }

        public function setLabel($label) {
            $this->label = $label;
        }

        public function setValue($value) {
            $this->value = $value;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function fill($selectName, $method) {
            if ($method == 'GET') {
                $req = $_GET;
            } else {
                $req = $_POST;
            }
            if ($req[$selectName] == $this->name) {
                $this->setSelected();
                return true;
            }
            return false;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'option.tpl');
            $this->Template->assign('id', $this->id);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('value', $this->value);
            $this->Template->assign('label', $this->label);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('selected', $this->selected);
            $string = preg_replace('/\s{2,}/sm', ' ', $this->Template->parse());
            return $string;
        }
    }
?>
