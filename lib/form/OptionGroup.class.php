<?php
    namespace Lib\Form;
    /**
     * [OptionGroup.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    class OptionGroup {
        protected $Template;
        protected $groupName = false;
        protected $options;

        public function __construct($groupName = null) {
            $this->groupName = $groupName;
        }

        public function setGroupName($groupName) {
            $this->groupName = $groupName;
        }

        public function registerOption(Option $option) {
            $this->options[] = $option;
        }

        public function fill($selectName, $method) {
            if (is_array($this->options)) {
                foreach ($this->options AS $Option) {
                    if ($Option->fill($selectName, $method)) {
                        return $Option;
                    }
                }
            }
            return false;
        }

        public function unselectAll() {
            foreach ($this->options AS $Option) {
                $Option->setSelected(true);
            }
            return true;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'optgroupHeader.tpl');
            $this->Template->assign('name', $this->groupName);
            $header = $this->Template->parse();
            $body = '';
            foreach ($this->options AS $value) {
                $body .= (string)$value;
            }
            $footer = $this->Template->parseSimple(\Config\Form::TPL_PATH . 'optgroupFooter.tpl');
            return $header . $body . $footer;
        }
    }
?>
