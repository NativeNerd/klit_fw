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
        /**
         * contains template object
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * contains id attribute
         * @var string
         */
        protected $id;
        /**
         * contains OptionGroup objects
         * @var array
         */
        protected $optiongroups;
        /**
         * contains Option objects
         * @var array
         */
        protected $options;
        /**
         * marked if select is disabled
         * @var boolean
         */
        protected $disabled = false;
        /**
         * marked if multiple selects are possible
         * @var boolean
         */
        protected $multiple = false;
        /**
         * contains size of select
         * @var int
         */
        protected $size = false;
        /**
         * triggered, if select has a selected option
         * @var boolean
         */
        protected $hasSelected = false;

        /**
         * set id atribute
         * @param string $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * set name attribute of select
         * @param  string $name
         */
        public function setName($name) {
            $this->name = $name;
        }

        /**
         * get name attribute
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * disable select, trigger
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
         * active multiple selects, trigger
         * @return boolean
         */
        public function setMultiple() {
            if ($this->multiple)
                $this->multiple = false;
            else
                $this->multiple = true;
            return true;
        }

        /**
         * sets displayed size of select
         * @param int $size
         */
        public function setSize($size) {
            $this->size = (int)$size;
        }

        /**
         * register OptionGroup object
         * @param \Lib\Form\OptionGroup $optionGroup
         */
        public function registerOptionGroup(OptionGroup $optionGroup) {
            $this->optiongroups[] = $optionGroup;
        }

        /**
         * register Option object
         * @param \Lib\Form\Option $option
         */
        public function registerOption(Option $option) {
            $this->options[] = $option;
        }

        /**
         * does select sent option
         * @param string $method
         * @return boolean
         */
        public function fill($method) {
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

        /**
         * searchs all options whether one is selected
         * if option==unselect, unselects all options
         * @param string $method
         * @param string $option
         * @return boolean
         */
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
