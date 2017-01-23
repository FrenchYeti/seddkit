<?php
namespace Seddkit;


/**
* Security Exception triggered by SEDDkit when a new weakness is detected.
* The message help developer to prevent a vulnerability
*/
class DevelopmentSecurityException extends \Exception
{
    const INJECTION_LVL = 1;
    const SESSION_LVL = 1;
    const AUTH_LVL = 1;
    const XSS_LVL = 1;
    const REFERENCE_LVL = 1;
    const ACL_LVL = 1;
    const CONFIG_LVL = 1;
    const COMM_LVL = 1;
    const OPENREDIRECT_LVL = 1;
    
    protected $message = null;
    
    public function __construct($subject_mix, $rulename_str, $context=null, \Exception $previous = null)
    {
        parent::__construct("", 0, $previous);
        
        // try to detect concatenation, cast, echo or print
        if($context === Seddkit::STRING_CONTEXT){
            
        }
        
        $trace = $this->getTrace()[count($this->getTrace())-1];
        
        var_dump($trace);
        
        $rule = Seddkit::RULE($rulename_str);
        
        if($subject_mix instanceof Tainted)
            $subject = $subject_mix->getVarname();
        else
            $subject = var_export($subject_mix, true);
        

        $this->message = (Seddkit::isBypassed($rulename_str)? "<pre>[*] BYPASSED : " : "<pre>[!] ");
        $this->message .= sprintf('<b>'.$rule['title'].'</b>',htmlentities($subject));
        $this->message .= "<br><i>File : ".htmlentities(isset($trace['file'])?$trace['file']:"[PHP Internals]").
            " , Line : ".((isset($trace['line'])?(int)$trace['line']:0)).
            ", ".(($trace['function']=='__toString')?'':"Function : {$trace['function']}()")."</i><br>";
        $this->message .= sprintf('%s<br>En savoir plus : %s<br>',htmlentities($rule['msg']),$rule['link']);
        
        if(Seddkit::CONFIG('PRINT_STATE') && ($subject_mix instanceof Tainted))
           $this->message .= $subject_mix->printState("<br>","&nbsp;");
        
        $this->message .= '</pre>';
        if(Seddkit::CONFIG('DISPLAY_ERR')){
            echo $this->message;
        }
        
        Observer::TRIGGER($rulename_str, $subject_mix);
        Seddkit::trace($this,Seddkit::DEV_TRACE);
    }
    
    
    public function __toString()
    {
        return $this->message;
    }
}
 
?>
