<?php

namespace Seddkit;

class Observer
{
    const NO_ARGS = 0;
    
    private static $_listener = array();
    
    public static function TRIGGER($eventname_str, $subject_obj=null)
    {
        if(!isset(self::$_listener[$eventname_str]))
            return false;
        
            
        $callbacks = self::$_listener[$eventname_str];
       
        $c = count($callbacks);
        
        for($i=0; $i<$c; $i++){
            if(!is_null($callbacks[$i]['subject']) ){
                if(($subject_obj === $callbacks[$i]['subject']) 
                    || ($subject_obj->isMemberOf($callbacks[$i]['subject'])) ){
                    $args = array($subject_obj);
                }else{
                    return false;
                }
            }elseif($callbacks[$i]['args'] !== self::NO_ARGS){
                $args = $callbacks[$i]['args'];
                array_unshift($args, $subject_obj);
            }else{
                $args = array();
            }
            
            call_user_func_array($callbacks[$i]['call'],$args);
        }    
    }
    
    
    /**
    *
    * If the callback has no argument except the subject, then $callback can be a string.
    * Else, if the callback expect arguments , pass an array like [method_name,args1,args2] ...
    * If available, the subject will always add as the first argument.
    *
    */
    public static function newListener($eventname_str, $callback, $callback_args=self::NO_ARGS, $subject_obj = null)
    {
        if(!isset(self::$_listener[$eventname_str]))
            self::$_listener[$eventname_str] = array();
        
        self::$_listener[$eventname_str][] = array('call'=>$callback,'args'=>$callback_args, 'subject'=>$subject_obj);
    }   
}

?>