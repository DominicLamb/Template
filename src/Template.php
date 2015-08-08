<?php 
namespace Schaflein\Template;
use \ArrayAccess;
use \RuntimeException;
class Template implements ArrayAccess {
	private $basePath;
	private $elements = array();
	private $files = array();

	public function __construct($basePath) {
		$this->basePath = $basePath;
	}
	public function addElement($key, $value) {
		$this->elements[$key] = $value;
	}
    public function addElementArray($data) {
        foreach($data as $key => $value) {
            $this->addElement($key, $value);
        }
    }
    public function addFile($filename) {
        $this->files[] = $filename;
    }
	public function getPath() {
		return $this->basePath;
	}
	private function loadFile($file) {
		$filename = $this->basePath . DIRECTORY_SEPARATOR . $file . '.json';
		if(!is_file($filename)) {
			throw new RuntimeException('Could not load template file: ' . $filename);
		}
		$data = file_get_contents($filename);
		if($data) {
			$data = json_decode($data, true);
			$this->addElementArray($data);
		}
	}
    public function loadFiles() {
        foreach($this->files as $file) {
			$this->loadFile($file);
        }
    }
	public function offsetExists($index) {
		return isset($this->elements[$index]);
	}
	public function offsetUnset($index) {
		unset($this->elements[$index]);
	}
	public function offsetGet($index) {
		return $this->elements[$index];
	}
	public function offsetSet($index, $value) {
		$this->elements[$index] = $value;
	}
}
?>