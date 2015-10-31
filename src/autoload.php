<?php

/**
 * Autoloader
 *
 * @version 1.0
 * @author Dominic
 */
spl_autoload_register(function($fqcn) {
	$extension = '.php';
	$classes = array(
		'Schaflein\\Template\\Renderer' => 'Renderer',
		'Schaflein\\Template\RenderTree' => 'RenderTree',
		'Schaflein\\Template\\Template' => 'Template',
		'Schaflein\\Template\\TemplateNamespace' => 'TemplateNamespace',
        'Schaflein\\Template\\TokenInterpreter' => 'TokenInterpreter'
	);
	if(isset($classes[$fqcn])) {
		include(__DIR__ . DIRECTORY_SEPARATOR . $classes[$fqcn] . $extension);
	}
});