<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

return [
	'css' => 'dist/main.bundle.css',
	'js' => 'dist/main.bundle.js',
	'rel' => [
		'main.polyfill.core',
		'ui.vue',
		'polus.vue.plugins.request',
		'polus.vue.plugins.notification',
		'polus.vue.plugins.confirm',
	],
	'skip_core' => true,
];