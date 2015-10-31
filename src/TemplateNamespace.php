<?php
namespace Schaflein\Template;
class TemplateNamespace {
	private $data = array();
	private $interpreter;
	private $name = '';
	public function __construct($name, $data = array(), $interpreter = 'TokenInterpreter') {
		$this->setAllData($data);
		$this->setName($name);
		$this->setInterpreter($interpreter);
	}
	/*
		Get all data in the template namespace.
	*/
	public function getAllData() {
		return $this->data;
	}
	/*
		Gets a single piece of information by key from this namespace.
	*/
	public function getData($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		}
		return null;
	}
	/*
		Return the name of this namespace
	*/
	public function getName() {
		return $this->name;
	}
	/*
		Replace all data in the namespace with the specified array.
	*/
	public function setAllData($data) {
		$this->data = $data;
	}
	/*
		Set a piece of data in the namespace by key.
	*/
	public function setData($key, $value) {
		$this->data[$key] = $value;
	}
	/*
		Sets the token reader for this namespace. Defaults to the included TokenInterpreter
	*/
	public function setInterpreter($interpreter) {
		$this->interpreter = 'Schaflein\Template\\' . $interpreter;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function interpret($string) {
		$interpreter = $this->interpreter;
		return $interpreter::interpret($string, $this);
	}
}
?>