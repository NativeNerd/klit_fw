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
        /**
         * contains template object
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * contains id of option
         * @var string
         */
        protected $id;
        /**
         * marked if selected
         * @var boolean
         */
        protected $selected;
        /**
         * marked if disabled
         * @var boolean
         */
        protected $disabled;
        /**
         * contains label attribute
         * @var string
         */
        protected $label;
        /**
         * contains name attribute
         * @var string
         */
        protected $name;
        /**
         * contains text value
         * @var string
         */
        protected $value;

        /**
         * Initializes class
         * @param string $name
         * @param string $value
         */
        public function __construct($name = null, $value = null) {
            $this->name = $name;
            $this->value = $value;
        }

        /**
         * called after clone
         */
        public function __clone() {
            $this->name = null;
            $this->value = null;
            $this->label = null;
        }

        /**
         * set id attribute
         * @param string $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * Assign option to group
         * @param \Lib\Form\OptionGroup $group
         * @return boolean
         */
        public function assignToGroup(OptionGroup $group) {
            $group->registerOption($this);
            return true;
        }

        /**
         * mark as selected, trigger
         * if $forceFalse = true, selected always gets true
         * @param boolean $forceFalse
         * @return boolean
         */
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

        /**
         * mark as disabled, trigger
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
         * Set label attribute
         * @param string $label
         */
        public function setLabel($label) {
            $this->label = $label;
        }

        /**
         * set option value
         * @param string $value
         */
        public function setValue($value) {
            $this->value = $value;
        }

        /**
         * set displayed text
         * @param string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * checks whether this option is sent by request
         * @param string $selectName
         * @param string $method
         * @return boolean
         */
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
