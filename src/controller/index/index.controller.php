<?php
    namespace Src\Controller;
    /**
     * [index.controller.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Was it does
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class index implements \Core\Implement\controller {
        protected $Query;
        protected $Template;
        protected $Permission;

        public function __construct() {
            $this->Query = \Lib\Query::getInstance();
            $this->Template = \Lib\Template::getInstance();
            $this->Permission = \Lib\Permission::getInstance();
            return ;
        }

        public function show($uri) {
            $this->Permission->useLaw('main.law');
            $this->Permission->useGroup('root.group');
            $this->Permission->assignGroup('root');
            $this->Template->open('index/index.tpl');
            $this->Template->show();
        }

        public function fallback() {

        }

        public function __desctruct() {

        }

    }

?>
