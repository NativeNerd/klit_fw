<?php
    namespace Config;
    /**
     * [Constant.config.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Constant {
        const MEXCEPTION_ELEVEL     = 7;
        const MEXCEPTION_EDOC       = 'core/error.html';

        const PATH_LIB              = 'lib/';
        const PATH_CORE             = 'core/';
        const PATH_CONFIG           = 'config/';
        const PATH_SRC              = 'src/';
        const PATH_VENDOR           = 'vendor/';
        const PATH_MEDIA            = 'media/';
        const PATH_ABSTRACT         = 'core/extendable/';
        const PATH_INTERFACE        = 'core/interfaces/';
        const PATH_TRAIT            = 'core/traits/';
        const PATH_CSS              = 'media/css/';
        const PATH_JS               = 'media/js/';
        const PATH_MODEL            = 'model/';
        const PATH_MAP              = 'map/';
        const PATH_CONTROLLER       = 'src/controller/';

        const FILE_COREEXT          = '.class.php';
        const FILE_LIBEXT           = '.class.php';
        const FILE_CONFIGEXT        = '.config.php';
        const FILE_MAPEXT           = '.map.php';
        const FILE_MODELEXT         = '.model.php';
        const FILE_CONTROLLEREXT    = '.controller.php';
        const FILE_IMPLEMENTEXT     = '.interface.php';
        const FILE_ABSTRACTEXT      = '.abstract.php';
    }
?>
