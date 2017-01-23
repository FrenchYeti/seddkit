<?php

namespace Seddkit;

class State
{
    protected $_tree = array();
    protected $_index = -1;
 
    
    public function __construct($tree = array())
    {
        $this->_tree = $tree;
    }
    
    private static function fnCall($fname_str, $fargs)
    {
        return array("name"=>$fname_str, "args"=>$fargs);
    }
    
    /**
    * Functionn which return a value create a new derivation
    */
    public function push($name_str, $operation_str=null, $args_mix=null)
    {
        array_push($this->_tree, array(
            "previous"=>$this->_index,
            "func"=>array(),
            "name"=>$name_str
        ));
        
        $this->_index = count($this->_tree)-1;
        if(!is_null($operation_str))
            $this->applyProcedure($operation_str, $args_mix);
    }    
    
    /**
    * Procedure which change a symbol value and not return the new value
    * require to apply() the procedure.
    */
    public function applyProcedure($procedure_str, $args_mix = null)
    {   
        array_push(
            $this->_tree[$this->_index]["func"],
            self::fnCall($procedure_str,$args_mix)
        );
    }
    
    public function updateVarName($name_str)
    {
        if($this->_index > -1)
            $this->_tree[$this->_index]['name'] = $name_str;
    }
    
    
    public function derive()
    {
        return new State($this->_tree);
    }
    
    /**
    * Print state to a string 
    */
    public function sprint($nl="\n",$space=" ")
    {
        $s = "<br>State (describe the history of call which affects the value) : $nl";
        $c = 1;
        
        for($i=0;$i<count($this->_tree);$i++){
            if($i>0){
            //  $s .= str_repeat($space,(5*$i)+$c+1)."|$nl";
                $s .= str_repeat($space,($i-1)*3)."+--";
            }else{
                $s .= '+ Input : '.$this->_tree[$i]["name"].$nl;
            }
            
            $var = "?";
            for($j=0; $j<count($this->_tree[$i]["func"]); $j++){
                if($this->_tree[$i-1]["name"] !== null) 
                    $var = $this->_tree[$i-1]["name"];
                
                $s .= "+ {$this->_tree[$i]["func"][$j]["name"]}({$this->_tree[$i-1]["name"]}) : ".$this->_tree[$i]["name"]."$nl";
            }
        }
        
        return $s;
    }
}

?>
