<?php
require_once __DIR__ . '/vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$classLoader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$classLoader->registerNamespaces(array(
		'WorldCat\\Discovery' => __DIR__ . '/src',
		'Guzzle' => __DIR__ . '/vendor/guzzle/src',
		'Symfony\\Component\\EventDispatcher' => __DIR__ . '/vendor/symfony/event-dispatcher'
));
$classLoader->register();