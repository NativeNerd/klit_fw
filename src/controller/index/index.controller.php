<?php
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
    namespace Src\Controller;
    class index implements \Core\Implement\controller {
        private $Bootstrap;
        private $Query;
        private $Template;

        public function __construct(\Core\Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
            $this->Query = $this->Bootstrap->getApplication('Query');
            $this->Template = $this->Bootstrap->getApplication('Template');
            return ;
        }

        public function show($uri) {
            var_dump($uri);
            $this->Template->open('index/index.tpl');
            $this->Template->show();
        }

        public function fallback() {

        }

        public function __desctruct() {

        }

    }

?>
