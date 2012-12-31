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
        /**
         * @var \Lib\Query
         */
        protected $Query;
        /**
         * @var \Lib\Template
         */
        protected $Template;
        /**
         * @var \Lib\Permission
         */
        protected $Permission;
        /**
         * @var \Lib\Form
         */
        protected $Form;

        public function __construct() {
            $this->Query = \Lib\Query\Query::getInstance();
            $this->Template = \Lib\Template\Template::getInstance();
            $this->Permission = \Lib\Permission\Permission::getInstance();
            $this->Form = \Lib\Form\Form::getInstance();
            return ;
        }

        public function _before($uri) {

        }

        public function _after($uri) {

        }

        protected function _loginForm() {
            $this->Form->openForm('login', 'index.php?main=index&action=login');
            $this->Form->addInput('text', 'username');
            $this->Form->addLabel('username', 'Benutzername:');
            $this->Form->addInput('password', 'password');
            $this->Form->addRegex('password', '/(.{10,})/');
            $this->Form->addLabel('password', 'Passwort: ');
            $this->Form->addSelect('gender');
            $this->Form->addOption('gender', 'male', 'Mann');
            $this->Form->addOption('gender', 'female', 'Frau');
            $this->Form->addLabel('gender', 'Dein Geschlecht: ');
            $this->Form->closeSelect('gender');
            $this->Form->addInput('text', 'age');
            $this->Form->addLabel('age', 'Wie alt bist du (optional)?');
            $this->Form->addInput('submit', 'submit', 'Anmelden');
            return ;
        }

        public function show($uri) {
            $this->_loginForm();
            $this->Template->open('index/index.tpl');
            $this->Template->show();
            return ;
        }

        public function login($uri) {
            $this->_loginForm();
            $parse = $this->Form->parseForm();

            $this->Template->assign('gender', $this->Form->getValue('gender'), \Config\Template::FILTER_HTML);
            $this->Template->assign('name', $this->Form->getValue('username'),  \Config\Template::FILTER_HTML);
            $this->Template->assign('password', $this->Form->getValue('password'), \Config\Template::FILTER_HTML);
            $this->Template->assign('age', $this->Form->getValue('age'), \Config\Template::FILTER_HTML);

            if (count($parse['false']) > 0 OR count($parse['empty']) > 1) {
                $this->Form->preFill();
                $this->Template->assign('error', 'Da ist was falsch...');
                $this->Template->open('index/index.tpl');
                $this->Template->show();
            } else {
                $this->Template->open('index/login.tpl');
                $this->Template->show();
            }
            return ;
        }

        public function _fallback($uri) {
            $this->Template->open('index/fallback.tpl');
            $this->Template->show();
            return ;
        }

        public function __desctruct() {

        }

    }

?>
