<?php
namespace Seddkit;


/**
 * Abstract class for all tainted string
 */
abstract class Tainted
{
    
    protected static $nextID = 0;
    
    protected $_hashCode = null;
    
    protected $_state = null;
    
    protected $_varname = null;
    
    
    protected function newHashCode()
    {
        $this->_hashCode = __CLASS__."::".self::$nextID++;
    }
    
	/**
	 * To indicate that the string is tainted
	 * @var boolean		True means the string is tainted. False otherwise
	 */
	protected $tainted = true;
		
	/**
	 * To tell if the given Tainted Object is tainted or not
	 * @param Tainted $Object	The Tainted class object
	 * @return boolean			Returns true if the string is tainted. False otherwise
	 */
	public function isTainted()
	{
		return $this->tainted;
	}
    
	
	/**
	 * To decontaminate a "Tainted" object
	 */
	public function decontaminate()
	{
		$this->tainted = false;
	}

	/**
	 * To taint a string i.e. to contaminate it.
	 */
	public function contaminate()
	{
		$this->tainted = true;
	}
    
    public function getVarname(){
        return $this->_varname;
    }
    
    /**
    *
    */
    abstract public function newDerivation($method_str, $args_mix=null);
    
    
    /**
    *
    */
    public function printState($nl="\n",$space=" ")
    {
        return $this->_state->sprint($nl, $space);
    }
    
    public function isMemberOf($parent_mix){
        foreach($parent_mix as $k=>$v){
            if($v instanceof Tainted){
                if($v->getVarname()===$this->getVarname()){
                    return true;
                }
            }
            
        }
        return false;
    }
    
    public function getHashCode()
    {
        return $this->_hashCode;
    }
    
    protected static $_counter  =0;
    
    /**
    * Guess the varname if is not set :
    * Start by scanning for $GLOBALS if $scope is NULL
    */
    public function guessVarName($scope=null, $prefix='$')
    {
        if(is_null($scope)) 
            $scope = $GLOBALS;
      
        foreach($scope as $k=>$v){
            
            if($v instanceof Tainted){
                if($this->isSelf($prefix.$k, $v)){
                    $this->_varname = $prefix.$k;
                    $this->_state->updateVarName($this->_varname);
                    return true;
                }
            }else if(($k!=='GLOBALS') && is_array($v) && count($v)>0){  
                if($this->guessVarName($v, $prefix.$k))
                    return true;
            }
        }
        
        return false;
    }
    
    /**
    *
    */
    public function isSelf($var_name,$var_value)
    {
        if($this->_hashCode == $var_value->_hashCode)
            return true;
        else
            return false;
    }
}

?>
