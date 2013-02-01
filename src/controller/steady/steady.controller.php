<?php
    namespace Src\Controller;
    /**
     * [index.controller.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class index extends \Core\Extendable\Controller implements \Core\Interfaces\controller {
        /**
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * @var \Lib\Form\Form
         */
        protected $Form;

        public function __construct() {
            $this->Template = new \Lib\Template\Template();
            return ;
        }

        public function _before($uri) {
            return ;
        }

        public function _after($uri) {
            return ;
        }

        public function steady($uri) {
            
        }

        public function _fallback($uri = null) {
            return ;
        }

        public function __desctruct() {

        }

    }

?>
