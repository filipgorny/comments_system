<?php

/********************************************************************
 *
 *  File's name: Link.php
 *  Script's author: Filip Markiewicz (www.filipmarkiewicz.pl)
 *
 *  Created: 20.11.2012r.
 *  Last modificated: 24.11.2014r.
 *
 ********************************************************************/

class Link {
	protected $path;
	protected $parameters = array();

	/**
	 *
	 * @param $path String - path to file, where the link directs
	 *        $parameters Array - associative array of parameters
	 *
	**/
	public function __construct($path, $parameters = array()) {
		if(!is_array($parameters))
		{
			throw new Exception("Argument @parameters must be an array!");	
		}		
		
		$this->path = $path;
		$this->parameters = $parameters;
	}
	
	/**
	 *
	 * @param $name String - parameter's name (name of the Get variable)
	 *        $value String - parameter's value
	 *
	 * Adds the parametr (Get variable) to object.
	 * 
	**/
	public function setParameter($name, $value) {
		$this->parameters[$name] = $value;
	}

	/**
	 *
	 * @param $name String - name of the parameter (Get variable)
	 *
	 * Returns value of parameter, whose name was given in the argument.
	 *
	**/
	public function getParameter($name) {
		if(isset($this->parameters[$name]))
		{
			return $this->parameters[$name];	
		}	
		else
		{
			return false;	
		}
	}

	/**
	 *
	 * @param $name String - name of the parameter (Get variable)
	 *
	 * Deletes the paremeter, whose name was given in the argument, from the object.
	 *
	**/	
	public function deleteParameter($name) {
		if(isset($this->parameters[$name]))
		{
			unset($this->parameters[$name]);	
		}	
	}
	
	/**
	 *
	 * Returns array with all parameters added to object.
	 *
	**/
	public function getParameters() {
		return $this->parameters;	
	}

	/**
	 *
	 * Returns ready and processed link in fallowing shape: path_to_file?parameter1=value1&amp;parameter2=value2...
	 *
	**/
	public function get() {
		$link = $this->path;
		
		if(count($this->parameters) > 0)
		{
			$array = array();
			
			foreach($this->parameters as $name => $value)
			{
				if(is_array($value)) {
					foreach($value as $single)
					{
						$array[] = $name.'[]='.$single;
					}					
				}
				else
				{
					$array[] = $name.'='.$value;	
				}	
			}
			
			$parameters = implode('&amp;', $array);
			
			$link .= '?'.$parameters;
		}
		
		return $link;	
	}

	/**
	 *
	 * Returns ready and processed to modrewrite link in fallowing shape: path_to_file/value1/value2...
	 *
	**/
	public function getForModRewrite() {
		$path = pathinfo($this->path);		
		
		$link = null;
		
		if(preg_match('/^[a-zA-Z0-9_\-]+$/', $path['dirname'])) $link .= '/'.$path['dirname'];		
		
		$link .= '/'.$path['filename'];		
		
		if(count($this->parameters)) $link .= '/'.implode('/', $this->parameters);		
		
		return $link;	
	}
	
	/**
	 *
	 * Returns the Link object created by current relative address of webpage.
	 *
	**/
	static public function create() {
		$array = $_GET;
		$path = $_SERVER['PHP_SELF'];	
		
		return new self($path, $array);	
	} 
}

?>