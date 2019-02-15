<?php
namespace Symbiotic\AcfPageBuilder\Block;

abstract class AbstractBlock {

	protected $_templateName;
	protected $_name;
	protected $_layout;
	protected $_path;
	protected $_acfLayout;

	public function __construct($layout = "", $templatePath = "", $templateBasePath = "") {
		$this->_layout = $layout ? $layout : [];
		$this->_templatePath = $templatePath ? $templatePath : 'page-blocks';
		$this->_templateBasePath = $templateBasePath ? $templateBasePath :'template-parts';
		$this->bootWidget();
	}

	/**
	 * This is the Widget Lifecycle.
	 */
	private function bootWidget() {
		$this->registerLayoutBefore();
		$this->addFields($this->registerLayoutFieldsBefore());
		$this->addLayout($this->registerLayout());
		$this->addFields($this->registerLayoutFieldsAfter());
		$this->registerLayoutAfter();

		$this->_acfLayout =    acf_layout(apply_filters('symbiotic/acf_page_builder/add_layout_after', $this->getLayout()));
		$this->_templateName = str_replace('_', '-',substr($this->_acfLayout->getKey(),1));
	}

	public function init() {

	}

	/**
	 * This is the default render Method
	 */
	public function render() {
		if($this->getTemplateName()) {
			$this->getWrapperStart();
			$this->getContainerStart();
			$this->includeTemplate();
			$this->getContainerEnd();
			$this->getWrapperEnd();
		}
	}

	abstract protected function registerLayout();

	/**
	 * Hook
	 * Called before register Layout
	 */
	protected function registerLayoutBefore() {}

	/**
	 * Hook
	 * Called after register Layout
	 */
	protected function registerLayoutAfter() {}


	/**
	 * You can register additional Sub Fields for an Layout before
	 * Return the Fields in an array
	 * @return Array
	 */
	protected function registerLayoutFieldsBefore() {}

	/**
	 * You can register additional Sub Fields for an Layout after
	 * Return the Fields in an array
	 * @return Array
	 */
	protected function registerLayoutFieldsAfter() {}

	/**
	 *
	 * It's used to combine all fields which are getting added inside
	 * the lifecycle together. Just some helper to fill the fields correct.
	 *
	 * @param array $fields
	 *
	 * @return bool
	 */
	protected function addFields($fields = []) {

		// fields not an array or empty
		if(!is_array($fields) || empty($fields)) {
			return false;
		}

		if(!isset($this->_layout['sub_fields'])) {
			$this->_layout['sub_fields'] = $fields;
		} else {
			$this->_layout['sub_fields'] = array_merge(
				$this->_layout['sub_fields'],
				$fields
			);
		}
	}

	/**
	 *
	 * Update the Layout Configuration
	 * @TODO: Not 100% tested with hooks
	 *
	 * @param array $layout
	 */
	protected function addLayout($layout = []) {
		//if(empty($this->_layout)) {
		$this->_layout = $layout;
		//}
	}

	public function getLayout() {
		return $this->_layout;
	}

	public function getName() {
		return $this->_name;
	}

	public function getTemplateName() {
		return $this->_templateName;
	}

	protected function includeTemplate($template_name = null) {
		if($template_name) {
			$template_file = locate_template($this->_templateBasePath . '/' . $template_name . '.php');
		} else {
			$template_file = locate_template($this->_templateBasePath . '/'. $this->_templatePath . '/' . $this->getPath(). $this->_templateName . '.php', false, false);
		}
		if ($template_file) include( $template_file );
	}

	protected function getPath() {
		return apply_filters('symbiotic/acf_page_builder/blocks/set_template_path', $this->_path);
	}

	protected function setPath($path) {
		$this->_path = $path;
	}

	public function getContainerStart() {
		if($this->hasContainer()) {
			echo '<div class="'. field('ve-settings-container') .'">';
		}
	}

	public function getContainerEnd() {
		if($this->hasContainer()) {
			echo '</div>';
		}
	}

	public function hasContainer() {
		return get_sub_field('ve-settings-container') !== null;
	}

	public function getWrapperStart() {
		$wrapperClass = field('ve-settings-wrapper') ? field('ve-settings-wrapper'):'';

		// 've-NAME-block.php
		$nodeType = explode('-', $this->getTemplateName());

		if(count($nodeType) < 1) {
			throw new \Exception('Blockname couldn\'t be set. Please check Name');
		}

		$blockname = field('ve-settings-data-node-type') ?
			field('ve-settings-data-node-type') :
			ucfirst($nodeType[1]) . 'Block';


		$disable_animation = field('ve-settings-disable-animation') ? field('ve-settings-disable-animation'): 0;

		echo '<div 
			data-animation="'.$disable_animation.'"
			data-node-type="'.$blockname.'"
			id="row-'.field('row-id').'" 
			class="page-block '. $wrapperClass .'">';
	}

	public function getWrapperEnd() {
		echo '</div>';
	}

	public function getActLayouts() {
		return $this->_acfLayout;
	}
}
