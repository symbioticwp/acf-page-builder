<?php

namespace Symbiotic\AcfPageBuilder;


/**
 * loader
 */
function loader() {
	AcfPageBuilder::getInstance()->enable();
}



/**
 * Hooks
 */
if (function_exists('add_action')) {
	add_action('init', __NAMESPACE__ . '\loader');
}