<?php
/** =========================================
* Security Exception Driven Development kit
* -----------------------------------------
* Usage : require("seddkit.php");
* -----------------------------------------
* @author FrenchYeti
* @version 1.0
=========================================== */


namespace Seddkit;

require_once(__DIR__.'/SeddkitException.php');
require_once(__DIR__.'/DevelopmentSecurityException.php');
require_once(__DIR__.'/Observer.php');
require_once(__DIR__.'/Tainted.php');
require_once(__DIR__.'/TaintedArray.php');
require_once(__DIR__.'/TaintedScalar.php');




/** 
* The main class, encapsulate almost accessible methods. 
*/
class Seddkit 
{    
    //const SEDDK_YAML = 1;
    
    //const SEDDK_JSON = 2;
    
    const SEDDK_ARRAY = 3;
    
    const DEV_TRACE = 0;
    const INTERNAL_TRACE = 1;
    
    const HTTP_TRANSPORT = true;
    const OFFLINE_TRANSPORT = false;
    
    private static $_traces = array(
        self::DEV_TRACE => array(),
        self::INTERNAL_TRACE => array()
    );

    private static $_rules = array();
    
    private static $_BYPASS = array();
    
    private static $_CONFIG = array(
        // -------- Features configuration ---------
        // Enable/disable taint checking
        'TAINT_CHECKING'=>true,
        // Enable/disable PHP configuration checking
        'CONFIGURATION_CHECKING'=>true,
        // Display err message 
        'DISPLAY_ERR'=>true,
        // =========== Context rules ===============
        // Default white list of allowed HTTP method 
        'HTTP_METHODS'=>'GET,POST,PUT,DELETE', 
        // List of tainted superglobals variable
        'GLOBALS_INPUTS'=>'_GET,_POST,_REQUEST',
        // By default, $_COOKIE is tainted
        'COOKIE_TAINTED'=>false,    
        /* 
        Characters allowed as HTTP URI Query part values
        See RFC3986 :
            query         = *( pchar / "/" / "?" )
            pchar         = unreserved / pct-encoded / sub-delims / ":" / "@"
            unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
            sub-delims    = "!" / "$" / "&" / "'" / "(" / ")" / "*" / "+" / "," / ";" / "="
            pct-encoded   = "%" HEXDIG HEXDIG
        */
        'QUERY_RE'=>'/^[a-zA-Z[\]0-9_]+$/',
        // Use of $_REQUEST is a weakness (due to the risk of overwrite a $_GET variable with a $_POST value)  
        'DISABLE_REQUEST_SGLOBAL'=>true,
        /* Turn super globals to immutable objects
        *  ! Experimental
        */
        'IMMUTABLE_SGLOBAL'=>false,
        
        /* ========== Inclusion rules ========== */
        'REGISTER_GLOBALS'=>false,
        // allow_url_fopen should be disable
        'OPEN_REMOTE_FILE'=>false,
        'INCLUDE_REMOTE_FILE'=>false,
        
        /* ========== Session Rules ============ */
        // gc_collect_cycles 
    );
    
    /**
    * To get a rule definition
    * The definition contains all helpful informations about how prevent the 
    * catched vulnerability.
    * 
    * @param string $rulename_str the name of the rule to get
    */
    public static function RULE($rulename_str)
    {
        if(isset(self::$_rules[$rulename_str])){
            return self::$_rules[$rulename_str];
        }else{
            throw new SeddkitException($rulename_str,SeddkitException::UNKNOW_RULE);
        }
          
    }
    
    /**
    * To get a directive value 
    */
    public static function CONFIG($param_str)
    {
        if(isset(self::$_CONFIG[$param_str]))
            return self::$_CONFIG[$param_str];
        else
            throw new SeddkitException($param_str,SeddkitException::UNKNOW_DIRECTIVE); 
    }
    
    
    /**
    * Initialize SEDDkit : import configuration
    */
    public static function init($config_mixed, $filetype_int=self::SEDDK_ARRAY)
    {
        if(filetype_int===self::SEDDK_ARRAY && is_array($config_mixed)){
            foreach($config_mixed as $param){
                if(isset(self::$_CONFIG[$param]))
                    self::$_CONFIG[$param] = $config_mixed[$param];
            }
        }
        /*
        elseif(filetype_int===self::SEDDK_JSON){
            // not implemented
        }
        elseif(filetype_int===self::SEDDK_YAML){
            // not implemented
        }*/
    }
    
    public static function importRules($filepath_str)
    {
        if(file_exists($filepath_str))
            self::$_rules = array_merge(self::$_rules, require($filepath_str));
        else
            throw new SeddkitException($filepath_str,SeddkitException::RULE_FILE_NOT_EXISTS);
    }
    
    /**
    * Compare PHP configuration versus SEDDkit rules
    */
    public static function checkPhpConfiguration()
    {    
        // Test for register_globals 
        if(!self::CONFIG('REGISTER_GLOBALS') 
            && !isset(self::$_BYPASS['REGISTER_GLOBALS']) 
            && ini_get('register_globals')==='1') 
                throw new DevelopmentSecurityException(null,'REGISTER_GLOBALS');
                
        // Test for Remote URL used from fopen() or file_get_contents() - risk of RFI
        if(!self::CONFIG('OPEN_REMOTE_FILE') 
            && !isset(self::$_BYPASS['OPEN_REMOTE_FILE']) 
            && ini_get('allow_url_fopen')==='1')
                throw new DevelopmentSecurityException(null,'OPEN_REMOTE_FILE');
        
        // Test for Remote URL used from include() or require() - risk of RFI
        if(!self::CONFIG('INCLUDE_REMOTE_FILE') 
            && !isset(self::$_BYPASS['INCLUDE_REMOTE_FILE']) 
            && ini_get('allow_url_include')==='1')
                throw new DevelopmentSecurityException(null,'INCLUDE_REMOTE_FILE');        
        
    }
        
    public static function contaminate($var, $varname_str, $http_bool=false, $globals_bool=false){
       
        if(is_scalar($var))
            return new TaintedScalar($var,$varname_str);
        else
            return new TaintedArray($var,$varname_str,$http_bool,$globals_bool);
    }
    
    public static function contaminateSuperGlobals()
    {
        $sg = self::CONFIG('GLOBALS_INPUTS');
        
        if(stripos($sg,'_GET') !== FALSE)
            $_GET = self::contaminate($_GET, '$_GET', self::HTTP_TRANSPORT, true);
        if(stripos($sg,'_POST') !== FALSE)
            $_POST = self::contaminate($_POST, '$_POST', self::HTTP_TRANSPORT, true);
        if(stripos($sg,'_REQUEST') !== FALSE)
            $_REQUEST = self::contaminate($_REQUEST, '$_REQUEST', self::HTTP_TRANSPORT, true);
        if(stripos($sg,'_FILE') !== FALSE)
            $_FILE = self::contaminate($_FILE, '$_FILE', self::HTTP_TRANSPORT, true);
        if(stripos($sg,'_ENV') !== FALSE)
            $_ENV = self::contaminate($_ENV, '$_ENV', self::OFFLINE_TRANSPORT, true);
        
        //var_dump($_GET);
    }
    
    public static function contaminateAll($var_arr,$http_bool=false){
        foreach($var_arr as $k=>$v){
            ${$v} = self::contaminate( ${$v}, $http_bool);
        }
    }
    
    public static function trace($message_str, $type_const=self::DEV_TRACE)
    {
        self::$_traces[$type_const]["".microtime()] = $message_str; 
    }
    
    public static function dumpTrace()
    {
        print_r(htmlentities(self::$_trace));
    }
    
    /*
        Allow to create local bypass
        Return an instance of a bypassed proxy 
    */
    public static function allow($rulename_str)
    {
        if(isset(self::$_CONFIG[$rulename_str])){
            self::$_BYPASS[$rulename_str]=true;
//var_dump(self::$_BYPASS);
        }
    }
    
    
    public static function disallow($rulename_str)
    {
        if(isset(self::$_CONFIG[$rulename_str])){
            unset(self::$_BYPASS[$rulename_str]);
//var_dump(self::$_BYPASS);
        }
    }
    
    public static function isBypassed($rulename_str)
    {
        return isset(self::$_BYPASS[$rulename_str]);
    }
}




class SecurityContext
{
    public static function newSqlInjectionContext($vars_arr)
    {
    }
    
    public static function newRceContext($vars_arr)
    {
        
    }
}

/* ****************************************************
*                 SEDDkit function
**************************************************** */


// TRACE method should triggert an exception

/**
@param $cfg_filepath string Config filepath
@param $cfg_format int Config format : SEDDK_YAML, SEDDK_JSON, SEDDK_ARRAY, SEDDK_XML
*/
function run($rulesfilepath_str, $cfg_filepath=null,$cfg_format=0){
    
    // import config file is exists
    if(!is_null($cfg_filepath) && $cfg_format>0){
        Seddkit::init($cfg_filepath,$cfg_format);
    }
    
    Seddkit::importRules($rulesfilepath_str);
    
    if(Seddkit::CONFIG('CONFIGURATION_CHECKING'))
        Seddkit::checkPhpConfiguration();
    
    if(Seddkit::CONFIG('GLOBALS_INPUTS'))
        Seddkit::contaminateSuperGlobals();
    
    //if(Seddkit::CONFIG('DISABLE_REQUEST_SGLOBAL'))
    //    Seddkit::disableRequestSG($_REQUEST);
    
    if(Seddkit::CONFIG('COOKIE_TAINTED'))
        Seddkit::contaminate($_COOKIE);
}


/* **********************************************
*           Overrided function
************************************************ */
function mysql_query(){
    throw new DevelopmentSecurityException("",'MYSQL_EXTENSION');
}

function import_request_variables(){
    throw new DevelopmentSecurityException("",'REQUEST_VAR_IMPORT');
}

function strlen($string_str){
    if($string_str instanceof Tainted)
        return $string_str->lenght();
    else
        return \strlen($string_str);
}

/**
* Override setcookie()
*/
function setcookie( $name, $value = "", $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false){
    
    if(Seddkit::CONFIG('COOKIE_HTTP_ONLY') && $httponly===false)
        throw new DevelopmentSecurityException("",'COOKIE_HTTP_ONLY');
    
    if(Seddkit::CONFIG('COOKIE_SECURE') && $secure===false)
        throw new DevelopmentSecurityException("",'COOKIE_SECURE');
    
    
    setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function addListener($eventname_str, $callback, $callback_args=Observer::NO_ARGS, $subject_obj = null){
    Observer::newListener($eventname_str, $callback, $callback_args, $subject_obj);
}

?>