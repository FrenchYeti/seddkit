<?php
namespace Seddkit;

/**
 * class for one tainted string
 */
class TaintedScalar extends Tainted
{
	/**
	 * String that is tainted
	 * @var mixed
	 */
	protected $data;

    protected $bypass = array();
 
    // Control access to the value
    // It is TRUE only when a derivation or a bypass happens  
    protected $_deriving = false;
    
    public function __clone()
    {
        $this->newHashCode();
        $this->_state = $this->_state->derive();
        $this->_varname = null;
        $this->data = $this->data;
        $this->bypass = $this->bypass;
    }
    
	/**
	 * Constructor of the class.
     * If empty, $data should be set to 'null' in order to keep the same behaviors with empty()
     *
	 * @param string $data		The string that is to be tainted
     * @param default_data      The default value if $data value is empty
	 */
	public function __construct($data, $varname_str=null)
	{
        $this->newHashCode();
        $this->_varname = $varname_str;
        $this->_state = new State();
        $this->_state->push($varname_str);   
		$this->data = $data;
	}
    
    public function bypass2string($reason_str)
    {
        $this->bypass["".microtime()] = $reason_str;
        
        return $this->data;
    }

    
	/**
	 * Function to trigger error when trying to use a string that is tainted
	 * @return string	The string that is tainted
	 */
	public function __toString()
	{
        // guess the name of the current variable if it's not set
        if(is_null($this->_varname) && !$this->_deriving)
            $this->guessVarName();
        
        //If the string is tainted, then trigger the error
		if(Seddkit::CONFIG('TAINT_CHECKING') and $this->isTainted() and !$this->_deriving)	//If the string is tainted, then trigger the error
			throw new DevelopmentSecurityException($this,'TAINT_CHECKING');
		
		return $this->bypass2string($this->_deriving? "New Derivation":"Bypass");
	}
	
	/**
	 * Not block serialize() but encapsulate the new value into a derived TaintedScalar
     *
	 * @return boolean Return FALSE because it systematically fail
	 */
	public function __sleep()
	{
         // guess the name of the current variable if it's not set
        //if(is_null($this->_varname))
        //    $this->guessVarName();
        
        //If the string is tainted, then trigger the error
		if(Seddkit::CONFIG('TAINT_CHECKING') and $this->isTainted() and !$this->_deriving)	
			throw new DevelopmentSecurityException($this,'TAINT_CHECKING_SERIALIZE');
		
		return ;
	}
    
    
	/**
	 * Trigger an exception if you try to unserialize a tainted value
     * 
	 * @return null Return NULL because it systematically fail
	 */
	public function __wake_up()
	{
         // guess the name of the current variable if it's not set
        //if(is_null($this->_varname))
        //    $this->guessVarName();
        
        //If the string is tainted, then trigger the error
		if(Seddkit::CONFIG('TAINT_CHECKING') and $this->isTainted() and !$this->_deriving)	
			throw new DevelopmentSecurityException($this,'TAINT_CHECKING_UNSERIALIZE');
		
		return $this;
	}
    
    /**
    *
    */
    public function newDerivation($method_str, $args_mix=null, $varname_str=null)
    {
        // guess the name of the current variable if it's not set
        if(is_null($this->_varname))
            $this->guessVarName();
        
        // create derivation
        $new = clone $this;
        $new->_state->push($varname_str, $method_str, $args_mix);
        
        // allow access to the value
        $this->_deriving = true;
        
        $new->data = \call_user_func_array($method_str,$args_mix);
        
        // block access to the value
        $this->_deriving = false;
        
        return $new;
    }

	/**
	 * 
	 * @return number
	 */
    public function length()
    {
        return strlen($this->data);
    }
}
?>
