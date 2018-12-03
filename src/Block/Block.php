<?php


namespace Symbiotic\AcfPageBuilder\Block;


class Block extends AbstractBlock {

	public function __construct($layout = []) {
		parent::__construct($layout);
	}

	protected function registerLayout() {
		return $this->_layout;
	}
}