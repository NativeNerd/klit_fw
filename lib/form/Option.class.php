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
        protected $id = false;
        protected $selected = false;
        protected $disabled = false;
        protected $label = false;
        protected $name = false;
        protected $value = false;

        public function setId($id) {
            $this->id = $id;
        }

        public function assignToGroup(OptionGroup $group) {
            $group->registerOption($this);
            return true;
        }

        public function setSelected() {
            if ($this->selected)
                $this->selected = false;
            else
                $this->selected = \Config\Form::VALUE_SELECTED;
            return true;
        }

        public function disable() {
            if ($this->disabled)
                $this->disabled = false;
            else
                $this->disabled = \Config\Form::VALUE_DISABLED;
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

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'option.tpl');
            $this->Template->assign('id', $this->id);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('value', $this->value);
            $this->Template->assign('label', $this->label);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('selected', $this->selected);
            $string =  $this->Template->parse();
            $string = preg_replace('/\s{2,}/sm', ' ', $string);
            return $string;
        }
    }
?>
