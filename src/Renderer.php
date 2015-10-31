<?php
namespace Schaflein\Template;
use \RuntimeException;

class Renderer {
    private $namespaces = array();
	/*
		Adds a new namespace to the list.
	*/
	public function addNamespace($name, $key, $data = array()) {
		$this->namespaces[$name] = new TemplateNamespace($key, $data);
	}
	/*
		Fetches a namespace by prefix.
	*/
	public function getNamespace($name) {
		if(!isset($this->namespaces[$name])) {
			throw new RuntimeException('Namespace not found: ' . $name);
		}
		return $this->namespaces[$name];
	}

	/*
		Converts a given template and tree into a parsed string.
	*/
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

	/*
		Finds the start of a token to evaluate.
	*/
	private function getTagStart($string, $start) {
		$pos = $start;
		while($pos && $string[$pos] != '<') {
			$pos--;
		}
		if($string[$pos] != '<') {
			return false;
		}
		return $pos;
	}

	/*
		Find the matching end section of a template tag.
	*/
	private function getTagEnd($string, $start) {
		$pos = $start;
		while(isset($string[$pos]) && $string[$pos] != '>') {
			$pos++;
		}
		if(!isset($string[$pos])) {
			return false;
		}
		return $pos;
	}

	/*
		Renders a node in the template tree.
	*/
	private function renderNode($node, $template) {
		$templateString = '';
		foreach($node as $element) {
			$elementString = $this->handleArgList($template[$element[0]], $element[2]);
			$templateString .= $this->renderNamespaces($elementString);
		}
		return $templateString;
	}

	private function renderNamespaces($elementString) {
		foreach($this->namespaces as $namespace) {
			$elementString = $this->renderNamespace($elementString, $namespace);
		}
		return $elementString;
	}
	private function handleArgList($string, $args) {
		/*
			Creates a temporary namespace, content will be replaced for
			each discovered argument.
		*/
		$namespace = new TemplateNamespace('ARG');
		$outString = $string;
		$nameLength = strlen($namespace->getName());
		$pos = 0;
		do {
			$pos = strpos($outString, $namespace->getName(), $pos + 1);
			if($pos !== false) {
				$index = $outString[$pos + $nameLength];
				if(is_numeric($index) && isset($args[$index - 1])) {
					$token_start = $this->getTagStart($outString, $pos);
					$token_end = $this->getTagEnd($outString, $pos);
					$token_parsed = '';

					if($token_start !== false && $token_end !== false) {
						$namespace->setAllData($args[$index - 1]);
						$token = substr($outString, $token_start, ($token_end - $token_start) + 1);
						$token_parsed = $namespace->interpret($token);
						$outString = substr_replace($outString, $token_parsed, $token_start, ($token_end - $token_start) + 1);
					}
					

				}
			}
		}
		while($pos !== false && isset($outString[$pos]));

		return $outString;
	}

	private function renderNamespace($elementString, $namespace) {
		$key = $namespace->getName();
		$outString = $elementString;

		$pos = 0;
		do {
			$pos = strpos($elementString, $key, $pos + 1);
			if($pos !== false) {
				$token_start = $this->getTagStart($outString, $pos);
				$token_end = $this->getTagEnd($outString, $pos);
				$token_parsed = '';

				if($token_start !== false && $token_end !== false) {
					$token = substr($outString, $token_start, ($token_end - $token_start) + 1);
					$token_parsed = $namespace->interpret($token);
					$outString = substr_replace($outString, $token_parsed, $token_start, ($token_end - $token_start) + 1);
				}
				
				$pos += strlen($token_parsed);
			}
		}
		while($pos !== false && isset($outString[$pos]));


		return $outString;
	}
}
?>