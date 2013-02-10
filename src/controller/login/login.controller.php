<?php
    namespace Src\Controller;
    /**
     * [login.controller.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class login extends \Core\Extendable\Controller implements \Core\Interfaces\controller {
        /**
         * @var \Lib\Query\Query
         */
        protected $query;
        /**
         * @var \Lib\Template\Template
         */
        protected $template;
        /**
         * @var \Lib\Permission\Permission
         */
        protected $permission;
        /**
         * @var \Lib\Form\Form
         */
        protected $Form;

        public function __construct() {
            $this->query = new \Lib\Query\Query();
            $this->template = new \Lib\Template\Template();
            $this->permission = \Lib\Permission\Permission::getInstance();
        }

        public function _before($uri) {

        }

        public function _after($uri) {

        }

        public function check($uri) {
			if( trim( $_POST['benutzer'] ) != ''  AND trim( $_POST['pw'] ) != '' ){
				echo '{ "success": 1, "message": "'.$_POST['benutzer'].'"}';
			}
        }

 
        public function _fallback($uri = null) {
            $this->template->loadTemplate('index/fallback.tpl');
            $this->template->display();
        }

        public function __desctruct() {

        }

    }

?>
