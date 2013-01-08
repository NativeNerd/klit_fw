<?php
    namespace Lib\Form;
    /**
     * [Label.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Label {
        /**
         * Contains template object
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * Contains attribute for
         * @var string
         */
        protected $for;
        /**
         * contains label value
         * @var string
         */
        protected $value;

        /**
         * called after clone
         */
        public function __clone() {
            $this->for = null;
            $this->value = null;
        }

        /**
         * set for attribute
         * @param string $to
         * @return boolean
         */
        public function assignTo($to) {
            $this->for = $to;
            return true;
        }

        /**
         * get for attribute
         * @return string
         */
        public function getAssignedTo() {
            return $this->for;
        }

        /**
         * set label value
         * @param string $value
         * @return boolean
         */
        public function setValue($value) {
            $this->value = $value;
            return true;
        }

        public function __toString() {
            $this->Template = new \Lib\Template\Template();
            $this->Template->open(\Config\Form::TPL_PATH . 'label.tpl');
            $this->Template->assign('for', $this->for);
            $this->Template->assign('value', $this->value);
            return $this->Template->parse();
        }
    }
?>
