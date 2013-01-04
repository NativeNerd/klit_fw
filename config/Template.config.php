<?php
    namespace Config;
    /**
     * [Template.config.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Template {
        // User defined const
        const DIR               = 'media/tpl/de/';
        const MASTER            = 'media/tpl/de/_master/';

        // Engineer defined const
        const REGEXP_IGNORE     = '/\{ignore\}(.+?)\{\/ignore\}/s';
        const REGEXP_DECLARE    = '/\{declare([\s]+)\(\$([\w]+)\)([\s]+)\((.*)\)\}\n/';
        const REGEXP_VARIABLE   = '/\{\$([\w:]+)\}/';
        const REGEXP_INTERNAL   = '/\{\$([\w.:]+)\}/';
        const REGEXP_CS_MID     = '([\s]+)((\$[\w:\.]+)([=<>!]{2})([\$\w:\.]+|true|false)|([\!]{0,1})([\w]+)\((\$[\w:\.]+)\))\}(.+?)';
        const REGEXP_CS_START   = '/\{(if|else)';
        const REGEXP_CS_STARTS  = '/\{(if\.|else\.)';
        const REGEXP_CS_END     = '{\/(if|else)\}/s';
        const REGEXP_CS_ENDS    = '\n/';
        const REGEXP_INCLUDE    = '/\{include \(([a-zA-Z0-9._\/]+)\)\}/';
        const REGEXP_FORM       = '/\{form \(([\w:]+)\)([\s]*)((\(([\w]+)\))|)\}/';
        const REGEXP_LABEL      = '/\{label \(([\w:]+)\)([\s]*)((\(([\w]+)\))|)\}/';
        const REGEXP_FOREACH    = '/\{foreach \(([\$\w\.:]+)\)\}(.+?)\{\/foreach\}/s';
        const UNDEFINIED_VAR    = '';

        const FILTER_HTML       = 1;
        const FILTER_QUOTES     = 2;
        const FILTER_BOOLEAN    = 4;
        const FILTER_INT        = 8;
        const FILTER_FLOAT      = 16;
        const FILTER_WORDS      = 32;
        const FILTER_MAIL       = 64;
        const FILTER_IP         = 128;
        const FILTER_URL        = 256;
        const FILTER_STRING     = 512;
    }

?>
