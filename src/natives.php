<?php

// if necessary change the namespace (set your focused namespace)
namespace FocusedNamespace;

?>
<?php


/* ----------- !!! NOT MODIFY !!! ---------------- */

/* SEDDkit classes */
require_once(__DIR__.'/SeddkitException.php');
require_once(__DIR__.'/DevelopmentSecurityException.php');
require_once(__DIR__.'/Observer.php');
require_once(__DIR__.'/Tainted.php');
require_once(__DIR__.'/TaintedArray.php');
require_once(__DIR__.'/TaintedScalar.php');
require_once(__DIR__.'/Seddkit.php');
require_once(__DIR__.'/State.php');


/**
@param $cfg_filepath string Config filepath
@param $cfg_format int Config format : SEDDK_YAML, SEDDK_JSON, SEDDK_ARRAY, SEDDK_XML
*/
function SEDDKIT($rulesfilepath_str, $cfg_filepath=null,$cfg_format=0){
    
    // import config file is exists
    if(!is_null($cfg_filepath) && $cfg_format>0){
        \Seddkit\Seddkit::init($cfg_filepath,$cfg_format);
    }
    
    \Seddkit\Seddkit::importRules($rulesfilepath_str);
    
    if(\Seddkit\Seddkit::CONFIG('CONFIGURATION_CHECKING'))
        \Seddkit\Seddkit::checkPhpConfiguration();
    
    if(\Seddkit\Seddkit::CONFIG('GLOBALS_INPUTS'))
        \Seddkit\Seddkit::contaminateSuperGlobals();
    
    //if(Seddkit::CONFIG('DISABLE_REQUEST_SGLOBAL'))
    //    Seddkit::disableRequestSG($_REQUEST);
    
    if(\Seddkit\Seddkit::CONFIG('COOKIE_TAINTED'))
        \Seddkit\Seddkit::contaminate($_COOKIE);
}


/* ***********************************************
*           Overrided standard function
************************************************ */

function strlen($string_str){
    if($string_str instanceof \Seddkit\Tainted)
        return (int)$string_str->lenght();
    else
        return \strlen($string_str);
}



/* ***********************************************
*           Overrided standard function
************************************************ */



// htmlentities($string, $quote_style, $charset, $double_encode)
function htmlentities(){
    $args = func_get_args();
    if(isset($args[0]) && ($args[0] instanceof \Seddkit\Tainted)){
        return $args[0]->newDerivation('htmlentities',$args);  
    }else{
        return \call_user_func_array('htmlentities',$args);
    } 
}

function substr(){
    $args = func_get_args();
    if(isset($args[0]) && ($args[0] instanceof \Seddkit\Tainted)){
        return $args[0]->newDerivation('substr',$args);  
    }else{
        return \call_user_func_array('substr',$args);
    } 
}

/* ***********************************************
*           Overrided riskly function
************************************************ */
function mysql_query(){
    throw new \Seddkit\DevelopmentSecurityException("",'MYSQL_EXTENSION');
}

function import_request_variables(){
    throw new \Seddkit\DevelopmentSecurityException("",'REQUEST_VAR_IMPORT');
}


/**
* Override setcookie()
* Detect :  
*   - No flag HTTP_ONLY
*   - No flag SECURE
*   - (TODO) Poor name if ued as Session cookie
*   - (TODO) Too long duration 
*   - (TODO) Poor domain restriction (allow for all subdomain)
*   - (TODO) Poor path restriction (increase the risk of cookie stealing if there is a XSS vulnerability in the other part of the applciation)
*/
function setcookie( $name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false){
    
    if(\Seddkit\Seddkit::CONFIG('COOKIE_HTTP_ONLY') && $httponly===false)
        throw new \Seddkit\DevelopmentSecurityException("",'COOKIE_HTTP_ONLY');
    
    if(\Seddkit\Seddkit::CONFIG('COOKIE_SECURE') && $secure===false)
        throw new \Seddkit\DevelopmentSecurityException("",'COOKIE_SECURE');
    
    
    setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function addListener($eventname_str, $callback, $callback_args=\Seddkit\Observer::NO_ARGS, $subject_obj = null){
    \Seddkit\Observer::newListener($eventname_str, $callback, $callback_args, $subject_obj);
}


?>
