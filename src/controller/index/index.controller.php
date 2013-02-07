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
            $this->Query = new \Lib\Query\Query();
            $this->Template = new \Lib\Template\Template();
            $this->Permission = \Lib\Permission\Permission::getInstance();
            return ;
        }

        public function _before($uri) {

        }

        public function _after($uri) {

        }

        protected function _loginForm() {
            return ;
            $this->Form = new \Lib\Form\Form();
            $this->Form->setId('login');
            $this->Form->usePost();
            $this->Form->setAction('index.php?main=index&action=show');

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

            $Gender_Female = clone $Gender_Male;
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

            $Age = new \Lib\Form\Select();
            $Age->setName('age');
            $Age->setSize(2);
            $Age_G1 = new \Lib\Form\OptionGroup('10 to 20');
            $Age->registerOptionGroup($Age_G1);
            $Age_G1_10 = new \Lib\Form\Option('age_10', '10 years');
            $Age_G1->registerOption($Age_G1_10);
            $Age_G1_11 = new \Lib\Form\Option('age_11', '11 years');
            $Age_G1->registerOption($Age_G1_11);
            $Age_G2 = new \Lib\Form\OptionGroup('21 to 30');
            $Age->registerOptionGroup($Age_G2);
            $Age_G2_21 = new \Lib\Form\Option('age_21', '21 years');
            $Age_G2->registerOption($Age_G2_21);
            $Age_G2_22 = new \Lib\Form\Option('age_22', '22 years');
            $Age_G2->registerOption($Age_G2_22);

            $Age_Label = new \Lib\Form\Label();
            $Age_Label->assignTo('age');
            $Age_Label->setValue('Alter:');

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
            $this->Form->registerSelect($Age);
            $this->Form->registerLabel($Age_Label);

            $this->Form->fill();

            $this->Template->registerFormClass($this->Form);
            return ;
        }

        public function show($uri) {
            $this->Template->loadTemplate('index/index.tpl');
            $this->Template->assign('value', true);
            $this->Template->display();
            return ;
        }

        public function login($uri) {
            $this->_loginForm();
            return ;
        }

        public function _fallback($uri = null) {
            $this->Template->loadTemplate('index/fallback.tpl');
            $this->Template->display();
        }

        public function __desctruct() {

        }

    }

?>
