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
    class Select {
        protected $Template;
        protected $id;
        protected $optiongroups;
        protected $options;
        protected $disabled = false;
        protected $multiple = false;
        protected $size = false;
        protected $hasSelected = false;

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
                $this->disabled = true;
            return true;
        }

        public function setMultiple() {
            if ($this->multiple)
                $this->multiple = false;
            else
                $this->multiple = true;
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

        public function fill($method) {
            /** @todo Possibly have more than one selected option :S */
            if ($method == 'GET') $req = $_GET;
            else $req = $_POST;
            if (isset($req[$this->name])) {
                if (is_object(($return = $this->fillHelper($method)))) {
                    $this->fillHelper($method, 'unselect');
                    $return->setSelected();
                }
            }
            return true;
        }

        protected function fillHelper($method, $option = 'search') {
            if ($option != 'unselect' AND $option != 'search') {
                $option = 'search';
            }
            if (is_array($this->optiongroups)) {
                foreach ($this->optiongroups AS $OptionGroup) {
                    if ($option == 'unselect') {
                        $OptionGroup->unselectAll();
                    } elseif (($Option = $OptionGroup->fill($this->name, $method))) {
                        $this->hasSelected = true;
                        return $Option;
                    }
                }
            }
            if (is_array($this->options)) {
                foreach ($this->options AS $Option) {
                    if ($option == 'unselect') {
                        $Option->setSelected(true);
                    } elseif ($Option->fill($this->name, $method)) {
                        $this->hasSelected = true;
                        return $Option;
                    }
                }
            }
            return true;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'selectHeader.tpl');
            $this->Template->assign('id', $this->id);
            $this->Template->assign('name', $this->name);
            $this->Template->assign('disabled', $this->disabled);
            $this->Template->assign('multiple', $this->multiple);
            $this->Template->assign('size', $this->size);
            $this->Template->assign('hasSelected', $this->hasSelected);

            $header =  $this->Template->parse();
            $header = preg_replace('/\s{2,}/sm', ' ', $header);

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
