<?php
    namespace Core;
    /**
     * [Mexception.class.php]
     * @version 1.1.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Handles Exceptions
     *
     * previous     now     what changed
     *              1.0.0   -
     * 1.0.0        1.1.0   Unterstützung für set_exception_handler und set_error_handler hinzugefügt
     *
     * MEXCEPTION_ELEVEL-Values:
     * -1   Don't display any text, simply (if given) the error number
     *  7   Display also Errors under PHPs E_LEVEL 16
     *  9   Display the full error
     *
     */
    class Mexception extends \Exception {
        /**
        * Throws a Exception on Regular way
        *
        * @param (object|string) $message
        */
        public function quit($message = null) {
            if ($message == null) {
                $message = 'keine';
            }
            if (is_object($message)) {
                $line = $message->line;
                $file = $message->file;
                $message = $message->message;
            } else {
                $line = null;
                $file = null;
            }
            if (\Config\Constant::MEXCEPTION_ELEVEL == -1) {
                if (is_numeric(substr($message, 0, 4))) {
                    $message = substr($message, 0, 4);
                } else {
                    $message = '(null)';
                }
            }
            $search = array(
                '{$errorvalue}',
                '{$errorfile}',
                '{$errorline}'
                );
            $replace = array(
                $message,
                $file,
                $line
            );
            echo str_replace($search, $replace,
                file_get_contents(\Lib\Helper\Helper::buildPath(\Config\Constant::MEXCEPTION_EDOC)));
            die();
        }

        /**
        * Handles an Error throw through the php-engine
        *
        * @param int $errno
        * @param string $errstr
        * @param string $errfile
        * @param int $errline
        * @param array $errcontext
        * @return null
        */
        public static function handle($errno, $errstr, $errfile, $errline, $errcontext) {
            if ($errno > 16)
                Mexception::quit($errno.': '.$errstr.' in '.$errfile.' at line '.$errline);
            elseif (\Config\Constant::MEXCEPTION_ELEVEL > 7)
                echo $errno.': '.$errstr.' in '.$errfile.' at line '.$errline."\n";
            else return ;
        }
    }

    set_exception_handler(array('\Core\Mexception', 'quit'));
    set_error_handler(array('\Core\Mexception', 'handle'), E_ALL);
?>
