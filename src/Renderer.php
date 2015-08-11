<?php
namespace Schaflein\Template;
use \RuntimeException;

class Renderer {
    private $namespaces = array();
	public function addNamespace($name, $key, $data = array()) {
		$this->namespaces[$name] = array(
			'key' => $key,
			'data' => $data
		);
	}
	public function addNamespaceData($name, $key, $value) {
		$namespace = $this->getNamespace($name);
        $namespace['data'][$key] = $value;
	}
	public function getNamespace($name) {
		if(!isset($this->namespaces[$name])) {
			throw new RuntimeException('Namespace not found: ' . $name);
		}
		return $this->namespaces[$name];
	}

	public function render(RenderTree $tree, Template $template) {
        $templateString = '';
		$root = $tree->getRoot();
		$rootNode = '';
		$template->loadFiles();

        if($root) {
		    $rootNode = $tree[$root];
        }

		if(!$rootNode) {
			throw new RuntimeException('Could not find root element: ' . $root);
		}
		$templateString = $this->renderNode($rootNode, $template);
        return $templateString;
	}

	private function renderNode($node, $template) {
		$templateString = '';
		foreach($node as $element) {
			$elementString = $this->handleArgs($template[$element[0]], $element[2]);
			$templateString .= $this->renderNamespaces($elementString);
		}
		return $templateString;
	}

	private function renderNamespaces($element) {
		foreach($this->namespaces as $namespace) {
			$this->renderNamespace($element, $namespace);
		}
		return $element;
	}
	private function handleArgs($string, $args) {
		$namespace = 'ARG';
		$outString = $string;
		$nameLength = strlen($namespace);
		$pos = 0;
		do {
			$pos = strpos($outString, $namespace, $pos + 1);
			if($pos !== false) {
				$index = $outString[$pos + $nameLength];
				if(is_numeric($index) && isset($args[$index - 1])) {
					$outString = $this->handleArg($outString, $pos, $args[$index - 1], $index);
				}
			}
		}
		while($pos !== false && isset($outString[$pos]));

		return $outString;
	}
	private function handleArg($string, $pos, $arg, $index) {
		$outString = $string;
		$tagStart = $pos;
		$tagEnd = $pos;
		$length = strlen($string);

		while($tagStart && $outString[$tagStart] != '<') {
			$tagStart--;
		}
		while($tagEnd < $length && $outString[$tagEnd] != '>') {
			$tagEnd++;
		}
		if($string[$tagStart] == '<' && isset($string[$tagEnd])) {
			/*
				Get string until tag starting position
			*/
			$outString = substr($string, 0, $tagStart);


			$token = substr($string, $tagStart + 1, ($tagEnd - $tagStart - 1));

			/*
				Get the relevant data from the argument for this token
			*/
			$data = self::getArgValue($arg, $token);
			/*
				Replace token with text
			*/
			$outString .= $data;

			/*
				Add the rest of the string
			*/
			$outString .= substr($string, $tagEnd + 1);
		}
		return $outString;
	}
    private function getArgValue($arg, $token) {
		$data = null;
		$pos = strpos($token, ':');
		if($pos !== false) {
			$index = substr($token, $pos + 1);
			if(isset($arg[$index])) {
				$data = $arg[$index];
			}
		}
		else
		{
			$data = $arg;
		}
		return $data;
	}
	private function renderNamespace($element, $namespace) {
		$data = $namespace['data'];
		return $element;
	}
}
?>