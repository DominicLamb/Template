<?php
namespace Schaflein\Template;
use \RuntimeException;

class Renderer {
    private $namespaces = array();
	public function addNamespace($name, $key) {
		$this->namespaces[$name] = array(
			'key' => $key,
			'data' => array()
		);
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
			$templateString .= $this->renderNamespaces($template[$element[0]]);
		}
		return $templateString;
	}
	private function renderNamespaces($element) {
		foreach($this->namespaces as $namespace) {
			$this->renderNamespace($element, $namespace);
		}
		return $element;
	}
	private function renderNamespace($element, $namespace) {
		return $element;
	}
}
?>