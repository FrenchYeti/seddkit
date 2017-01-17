<?php
 
return array(
        // Enable/disable taint checking
        'TAINT_CHECKING'=>array(
            'link'=>'',
            'owaspt10'=>'I/O Control',
            'msg'=>'Check the value versus the expected format and the context of use',
            'title'=>'Unchecked variable : you try to use the variable %s without sanitize it.'
        ),
        // Default white list of allowed HTTP method 
        'HTTP_METHODS'=>'GET,POST,PUT,DELETE', 
        'HTTP_TRACE_METHOD'=>array(
            'link'=>'',
            'owaspt10'=>'Session,XSS',
            'msg'=>'The TRACE HTTP method should be disallow in'.
                'order to prevent session hijacking via XST attack.'.
                'TRACE methods allow an attacker to bypass the HTTP_ONLY cookie flag protection.',
            'title'=>'The HTTP method TRACE should be disabled'
        ),
        // By default, $_COOKIE is tainted
        'COOKIE_TAINTED'=>array(
            'link'=>'',
            'owaspt10'=>'',
            'msg'=>'',
            'title'=>''
        ),    
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
        'REGISTER_GLOBALS'=>array(
            'link'=>'',
            'owaspt10'=>'Configuration',
            'msg'=>'[REGISTER_GLOBALS] register_globals should not be used for security reason.',
            
        ),
        // allow_url_fopen should be disable
        'OPEN_REMOTE_FILE'=>array(
            'link'=>'',
            'owaspt10'=>'',
            'msg'=>'[OPEN_REMOTE_FILE] allow_url_fopen should be disallow in order to prevent remote file inclusion.',
            'title'=>''
        ),
        'INCLUDE_REMOTE_FILE'=>array(
            'link'=>'',
            'owaspt10'=>'Configuration',
            'msg'=>'[INCLUDE_REMOTE_FILE] allow_url_include should be disallow in order to prevent remote file inclusion.',
            
        ),
        /* ========== Session Rules ============ */
        // gc_collect_cycles,
        'MYSQL_EXTENSION'=>array(
            'link'=>'',
            'owaspt10'=>'Configuration, Injection',
            'msg'=>'[MYSQL] The MySQL PHP extension is deprecate and should not be used.',
        ),
        'MYSQL_QUERY'=>array(
            'link'=>'',
            'owaspt10'=>'Configuration, Injection',
            'msg'=>'[MYSQL_QUERY] The function mysql_query() is deprecate and should not be used.',
        ),
        'REQUEST_VAR_IMPORT'=>array(
            'link'=>'',
            'owaspt10'=>'I/O Control, Injection',
            'msg'=>'[REQUEST_VAR_IMPORT] The function import_request_variables() is deprecate since PHP 5.3.0 and it\'s removed in PHP 5.4.0. It should not be used for security reasons (RCE injection).',
        )
    );
?>
