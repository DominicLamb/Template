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

    /*
        A simple string fetched from a file should render as displayed and
        without modification.
    */
    public function testRenderSimpleString() {
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple string', $string);
    }

    /*
        When a string is included multiple times at the same position, it
        should render the same string multiple times without modification.
    */
    public function testRenderRepeatedSimpleString() {
		$this->template->addFile('test');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->add('simpleString', 'root');
		$this->tree->setRoot('root');

		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('This is a simple stringThis is a simple stringThis is a simple string', $string);
    }

    /*
        Simply asserts that a namespace has been created when required
    */
	public function testAddNamespace() {
		$this->renderer->addNamespace('test', 'TEST');
		$namespace = $this->renderer->getNamespace('test');
		$this->assertNotEmpty($namespace);
	}

    /*
        When an argument is passed to a template string, interpret
        references to it.
    */
	public function testHandleSimpleFirstArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleFirstArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is blue', $string);
	}

    /*
        Interpreting a string should occur even if the string consists
        entirely of a token to be interpreted
    */
	public function testHandleSimpleJustArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleJustArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blue', $string);
	}

    /*
        If multiple arguments are passed, references should be interpreted
        based on the order in which the arguments were passed.
    */
	public function testHandleSimpleMultipleArg() {
		$this->template->addFile('test');
		$this->tree->add('simpleMultipleArg', 'root', 'blue', 'red', 'green');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blue and red and green', $string);
	}

    /*
        Ensures that token boundaries do not cause issues for the interpreter
        even if they're right next to each other. Tests that the string pointer
        does not get lost when interpreting the string.
    */
    public function testHandleTouchingSingleArg() {
		$this->template->addFile('test');
		$this->tree->add('touchingSingleArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blueblueblue', $string);
	}

    /*
        This test puts multiple tokens next to each other and asks that they
        all be interpreted in turn, to test that the token replacement
        doesn't get lost along the way.
    */
    public function testHandleTouchingMultipleArg() {
		$this->template->addFile('test');
		$this->tree->add('touchingMultipleArg', 'root', 'blue', 'red', 'green');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('blueredgreen', $string);
	}



    /*
        Tests that a partial token without an opening bracket will
        not be interpreted.
    */
	public function testHandleBadTokenStart() {
		$this->template->addFile('test');
		$this->tree->add('badTokenStart', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is ARG1>', $string);
	}

    /*
        Tests that a partial token without a closing bracket will
        not be interpreted.
    */
	public function testHandleBadTokenEnd() {
		$this->template->addFile('test');
		$this->tree->add('badTokenEnd', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARG1', $string);
	}

    /*
        This test ensurse that if a reference number is omitted,
        no replacement will take place.
    */
	public function testHandleBadTokenIndex() {
		$this->template->addFile('test');
		$this->tree->add('badTokenIndex', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARG>', $string);
	}

    /*
        Arguments cannot be nested, to avoid injection attacks.
        This test guarnatees this assertion.
    */
	public function testHandleNestedFirstArg() {
		$this->template->addFile('test');
		$this->tree->add('nestedFirstArg', 'root', 'blue');
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is <ARblueG1>', $string);
	}

    /*
        Ensures that array syntax works, and fetches the correct index
    */
	public function testHandleArrayArg() {
		$this->template->addFile('test');
		$this->tree->add('testArrayToken', 'root', array('test' => 'string', 'data' => 'red'));
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is red', $string);
	}

    /*
        If the index does not exist, the token is removed from the
        replacement string.
    */
	public function testHandleArrayArgInvalidIndex() {
		$this->template->addFile('test');
		$this->tree->add('testArrayToken', 'root', array('test' => 'string'));
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Blue is ', $string);
	}

	public function testSimpleNamespaceToken() {
		$this->template->addFile('test');
		$this->tree->add('namespaceSimpleArg', 'root');
		$this->renderer->addNamespace('SHORT', 'SHORT', array('TEST' => 'Example'));
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('Example namespace!', $string);
	}

	public function testNamespaceSameToken() {
		$this->template->addFile('test');
		$this->tree->add('namespaceSameArg', 'root');
		$this->renderer->addNamespace('SHORT', 'SHORT', array('TEST' => 'Example'));
		$this->tree->setRoot('root');
		$string = $this->renderer->render($this->tree, $this->template);
		$this->assertSame('ExampleExample Example namespace!', $string);
	}
}
?>