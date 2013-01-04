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
        protected $Template;

        protected $for;
        protected $value;

        public function assignTo($to) {
            $this->for = $to;
            return true;
        }

        public function getAssignedTo() {
            return $this->for;
        }

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
