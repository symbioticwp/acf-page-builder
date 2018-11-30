<?php


namespace Symbiotic\AcfPageBuilder\Blocks;


class Block extends AbstractBlock {

	public function __construct($layout = []) {
		parent::__construct($layout);
	}

	protected function registerLayout() {
		return $this->_layout;
	}
}