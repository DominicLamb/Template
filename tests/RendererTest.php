<?php
use \RuntimeException;

class RendererTest extends PHPUnit_Framework_TestCase { 
	private $renderer;
	private $tree;
	private $template;
    public function SetUp() {
		$path = __DIR__;
		$this->renderer = new \ACMS\Template\Renderer();
		$this->tree = new \ACMS\Template\RenderTree();
		$this->template = new \ACMS\Template\Template($path . DIRECTORY_SEPARATOR . 'test_template/');
		
    }
	/**
	 * Ensure that an empty tree throws an exception - Cannot render anything
	 * @expectedException RuntimeException
	 */
	public function testRenderEmptyTreeThrowsException() {
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('', $string);
	}
    public function testRenderSimpleString() {
		$path = $this->template->getPath();
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple string', $string);
    }

    public function testRenderRepeatedSimpleString() {
		$path = $this->template->getPath();
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple stringThis is a simple stringThis is a simple string', $string);
    }
}
?>