<?php
use \RuntimeException;

class RendererTest extends PHPUnit_Framework_TestCase { 
	private $renderer;
	private $tree;
	private $template;
    public function SetUp() {
		$path = __DIR__;
		$this->renderer = new \Schaflein\Template\Renderer();
		$this->tree = new \Schaflein\Template\RenderTree();
		$this->template = new \Schaflein\Template\Template($path . DIRECTORY_SEPARATOR . 'test_template/');
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
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple string', $string);
    }

    public function testRenderRepeatedSimpleString() {
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple stringThis is a simple stringThis is a simple string', $string);
    }
	public function testAddNamespace() {
		$this->renderer->addNamespace('test', 'TEST');
		$namespace = $this->renderer->getNamespace('test');
		$this->assertCount(2, $namespace);
	}

	public function testHandleSimpleFirstArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleFirstArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is blue', $string);
	}

	public function testHandleSimpleJustArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleJustArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blue', $string);
	}

	public function testHandleSimpleMultipleArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleMultipleArg', 'root', 'blue', 'red', 'green');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blue and red and green', $string);
	}




	public function testHandleBadTokenStart() {
		$this->template->addFile('test');
		$this->tree->add('badTokenStart', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is ARG1>', $string);
	}

	public function testHandleBadTokenEnd() {
		$this->template->addFile('test');
		$this->tree->add('badTokenEnd', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARG1', $string);
	}

	public function testHandleBadTokenIndex() {
		$this->template->addFile('test');
		$this->tree->add('badTokenIndex', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARG>', $string);
	}

	public function testHandleNestedFirstArg() {
		$this->template->addFile('test');
		$this->tree->add('nestedFirstArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARblueG1>', $string);
	}
}
?>