<?php 
namespace Schaflein\Template;
use \ArrayAccess;

class RenderTree implements ArrayAccess {
	private $root;
	private $list;
	public function __construct() {
	}
	/*
		Get the root element from which a template will be parsed
	*/
    public function getRoot() {
        return $this->root;
    }
	/*
		Defines or moves the root element in a template
	*/
	public function setRoot($root) {
		$this->root = $root;
	}
	/*
		Adds a new element to the list
	*/
	public function add() {
		$args = func_get_args();
		$element_name = array_shift($args);
		$container_name = array_shift($args);
		$this->list[$container_name][] = array($element_name, $container_name, $args);
	}
	public function offsetExists($index) {
		return isset($this->list[$index]);
	}
	public function offsetUnset($index) {
		unset($this->list[$index]);
	}
	public function offsetGet($index) {
		return $this->list[$index];
	}
	public function offsetSet($index, $value) {
		$this->list[$index] = $value;
	}
}
?>