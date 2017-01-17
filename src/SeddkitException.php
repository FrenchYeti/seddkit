<?php
namespace Seddkit;


/** *************************************
* Internal SEDDkit exception
* It happens in case of misuse or misconfiguration (unknow rulename)
*/
class SeddkitException extends \Exception
{    
    const UNKNOW_RULE = 1;
    const UNKNOW_DIRECTIVE = 2;
    const UNKNOW_OFFSET = 3;
    const UNSUPPORTED_VAR_TYPE = 4;
    const RULE_FILE_NOT_EXISTS = 5;

    /**
    * Predefined exception message template 
    */
    private static $_template = array(
        self::UNKNOW_RULE => 'The rule "%s" is unknown (code:%i)',
        self::UNKNOW_DIRECTIVE => 'The configuration directive "%s" is unknown (code:%i)',
        self::UNKNOW_OFFSET => 'The offset "%s" not exists (code:%i)',
        self::UNSUPPORTED_VAR_TYPE => 'The data type "%s" can\'t be tainted (code:%i)',
        self::RULE_FILE_NOT_EXISTS => 'The rule file "%s" cannot be imported because it does not exists (code:%i)'
    );
   
    /**
    * To construct a new internal SeddkitException
    */
    public function __construct($subject_str, $code = 0, \Exception $previous = null)
    {
        parent::__construct("", $code, $previous);
        
        if($code>0 && isset(self::$_template[$code]))
            $this->message = "[SEDDkit] Internal error : ".sprintf(self::$_template[$code],$subject_str,$code);
        else
            $this->message = "[SEDDkit] An unknow internal error happened. See trace for more details";
        
        Seddkit::trace($this,Seddkit::INTERNAL_TRACE);
    }
    
    
    public function __toString()
    {
        return $this->message;
    }
}

?>