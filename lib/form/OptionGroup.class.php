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
        /**
         * contains template object
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * contains group name
         * @var string
         */
        protected $groupName = false;
        /**
         * contains option objects
         * @var array
         */
        protected $options;

        /**
         * Initializes class
         * @param string $groupName
         */
        public function __construct($groupName = null) {
            $this->groupName = $groupName;
        }

        /**
         * set group name
         * @param string $groupName
         */
        public function setGroupName($groupName) {
            $this->groupName = $groupName;
        }

        /**
         * register option into this group
         * @param \Lib\Form\Option $option
         */
        public function registerOption(Option $option) {
            $this->options[] = $option;
        }

        /**
         * checks whether an option of this group is sent by request
         * @param string $selectName
         * @param string $method
         * @return boolean
         */
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

        /**
         * unselect all registered options in this group
         * @return boolean
         */
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
