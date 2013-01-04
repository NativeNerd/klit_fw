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
         * @var \Lib\Query\Query
         */
        protected $Query;
        /**
         * @var \Lib\Template\Template
         */
        protected $Template;
        /**
         * @var \Lib\Permission\Permission
         */
        protected $Permission;
        /**
         * @var \Lib\Form\Form
         */
        protected $Form;

        public function __construct() {
            $this->Query = \Lib\Query\Query::getInstance();
            $this->Template = new \Lib\Template\Template();
            $this->Permission = \Lib\Permission\Permission::getInstance();
            return ;
        }

        public function _before($uri) {

        }

        public function _after($uri) {

        }

        protected function _loginForm() {
            $this->Form = new \Lib\Form\Form();
            $this->Form->setId('login');
            $this->Form->usePost();
            $this->Form->setAction('index.php?main=index&action=login');

            $Username = new \Lib\Form\Textfield();
            $Username->setName('username');

            $Password = new \Lib\Form\Passwordfield();
            $Password->setName('password');

            $Gender = new \Lib\Form\Select();
            $Gender->setName('gender');
            $Gender_Male = new \Lib\Form\Option();
            $Gender_Male->setName('male');
            $Gender_Male->setValue('Mann');
            $Gender->registerOption($Gender_Male);

            $Gender_Female = new \Lib\Form\Option();
            $Gender_Female->setName('female');
            $Gender_Female->setValue('Frau');
            $Gender_Female->setSelected();
            $Gender->registerOption($Gender_Female);

            $Username_Label = new \Lib\Form\Label();
            $Username_Label->assignTo('username');
            $Username_Label->setValue('Benutzername:');

            $Password_Label = new \Lib\Form\Label();
            $Password_Label->assignTo('password');
            $Password_Label->setValue('Password:');

            $Gender_Label = new \Lib\Form\Label();
            $Gender_Label->assignTo('gender');
            $Gender_Label->setValue('Geschlecht:');

            $Submit = new \Lib\Form\Button();
            $Submit->setName('submit');
            $Submit->setValue('Absenden');

            $this->Form->registerSelect($Gender);
            $this->Form->registerInput($Username);
            $this->Form->registerInput($Password);
            $this->Form->registerLabel($Username_Label);
            $this->Form->registerLabel($Password_Label);
            $this->Form->registerLabel($Gender_Label);
            $this->Form->registerInput($Submit);
            $this->Template->registerFormClass($this->Form);
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
            return ;
        }

        public function _fallback($uri = null) {
            $this->Template->open('index/fallback.tpl');
            $this->Template->show();
            return ;
        }

        public function __desctruct() {

        }

    }

?>
