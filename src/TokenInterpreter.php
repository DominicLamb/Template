<?php
namespace Schaflein\Template;

class TokenInterpreter {
	/*
		Interprets a given template tag, returns a parsed string
		to replace the tag with.
	*/
	public static function interpret($string, $namespace) {
		$token = substr($string, 1, -1);
		$data = self::getArgValue($namespace, $token);

		return $data;
	}
    private static function getArgValue($arg, $token) {
		$data = null;
		$pos = strpos($token, ':');
		if($pos !== false) {
			$index = substr($token, $pos + 1);
			$data = $arg->getData($index);
		}
		else
		{
			$argdata = $arg->getAllData();
			if(is_scalar($argdata)) {
				$data = $argdata;
			}
		}
		return $data;
	}
}
?>