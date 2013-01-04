<?php
    namespace Lib\Form;
    /**
     * [Select.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class Select extends InputField {
        protected $Template;
        protected $id;
        protected $optiongroups;
        protected $options;
        protected $disabled = false;
        protected $multiple = false;
        protected $size = false;

        public function setId($id) {
            $this->id = $id;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getName() {
            return $this->name;
        }

        public function disable() {
            if ($this->disabled)
                $this->disabled = false;
            else
                $this->disabled = \Config\Form::VALUE_DISABLED;
            return true;
        }

        public function setMultiple() {
            if ($this->multiple)
                $this->multiple = false;
            else
                $this->multiple = \Config\Form::VALUE_MULTIPLE;
            return true;
        }

        public function setSize($size) {
            $this->size = (int)$size;
        }

        public function registerOptionGroup(OptionGroup $optionGroup) {
            $this->optiongroups[] = $optionGroup;
        }

        public function registerOption(Option $option) {
            $this->options[] = $option;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'selectHeader.tpl');
            $this->Template->assign('id', $this->id);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('multiple', $this->multiple);
            $this->Template->assign('size', $this->size);
            $header = $this->Template->parse();
            $footer = $this->Template->parseSimple(\Config\Form::TPL_PATH . 'selectFooter.tpl');
            $body = '';
            if (is_array($this->optiongroups)) {
                foreach ($this->optiongroups AS $value) {
                    $body .= (string)$value;
                }
            }
            if (is_array($this->options)) {
                foreach ($this->options AS $value) {
                    $body .= (string)$value;
                }
            }
            return $header . $body . $footer;
        }
    }
?>
