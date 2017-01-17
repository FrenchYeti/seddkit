<?php
namespace Seddkit;


/**
 * Abstract class for all tainted string
 */
abstract class Tainted
{
	protected $_varname = null;
    
	/**
	 * Enables/Disables the taint checking function
	 * @var boolean		True enables the taint checking function. False disables it 
	 */
	public static $TaintChecking = true;
	
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
}

?>