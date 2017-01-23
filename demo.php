
<?php
/*
* This first php block is only used for init SEDDkit. 
*/

// Choose your work namespace 
namespace FocusedNamespace;

// import overrided natives functions
// Set the namespace of this file in the same of above
require_once('src/natives.php');

// if you don't want modifiy your php.ini when demo,
// you can explicitly allow a rule like that : 
// \Seddkit\Seddkit::allow('OPEN_REMOTE_FILE');

// init seddkit
SEDDKIT("./rules/rules.php");
?>


<?php
/*
* All others php blocks shall be below than SEDDKIT() call
*/

if(isset($_GET['name'])){
    
    $a = htmlentities($_GET['name']);
    $b = substr($a,1,1);
    
    echo $b;
}else{
    echo "name is not set";
}

?>
