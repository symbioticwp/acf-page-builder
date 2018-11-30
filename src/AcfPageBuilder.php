<?php
namespace Symbiotic\AcfPageBuilder;

use Mj\Frontend\Widgets\AbstractWidget;
use Symbiotic\AcfPageBuilder\Blocks\AbstractBlock;
use Symbiotic\AcfPageBuilder\Blocks\Block;

class AcfPageBuilder {

	private $_enabled;
	private $_layouts;

	public static function getInstance()
	{
		static $inst = null;
		if ($inst === null) {
			$inst = new AcfPageBuilder();
		}
		return $inst;
	}

	public function __construct() {
		$this->_layouts = [];
		add_filter( 'the_content', [&$this, "filter_the_content"]);
	}

	private function registerEditorFields() {
		do_action('symbiotic/acf_page_builder/register_page_blocks', $this);

		$fields = [
			acf_flexible_content([
				'name'         => 'mj-visual-editor-flexible',
				'label'        => esc_attr__('Page Builder'),
				'button_label' => esc_attr__('Add Block'),
				'layouts'      =>  $this->getLayoutFields()
			])
		];

		acf_field_group( [
			'title'    => 'layout_builder',
			'fields'   => $fields,
			'style'    => 'seamless',
			'location' => $this->getLocations(),
		]);
	}

	/**
	 * Handle Block layouts
	 *
	 * @param {mixed} $layout
	 */
	public function addLayout($layout) {

		/**
		 * Get sure that $layout is either array or derived from AbstrackBlock
		 */
		if(!$layout instanceof AbstractBlock && !is_array($layout)) {
			throw new \Exception(
				'The parameter given to addLayout has to be an array' .
				' or a base class of Symbiotic/Blocks/AbstractBlock');
		}

		/**
		 * if layout is an array
		 */
		if(!$layout instanceof AbstractBlock) {
			$layout = new Block($layout);
		}

		// Possible way to adjust the layout array
//		$layoutConfig = apply_filters(
//			'symbiotic/acf_page_builder/add_layout_after',
//			$layout->getLayout());



		// Create a new Field Layout from our Config
		//$acfLayoutObj = acf_layout($layoutConfig);




		// Build our Custom Layout array
//		$this->_layouts[] = [
//			'template_name' =>
//				str_replace('_', '-',substr($acfLayoutObj->getKey(),1)),
//			'layout_obj' => $acfLayoutObj,
//			'block_obj' => $layout
//		];

		$this->_layouts[] = $layout;
	}

	public function filter_the_content( $content ) {
		if($this->_enabled) {
			// Check if we're inside the main loop in a single post page.
			if ( in_the_loop() && is_main_query() && have_rows('mj-visual-editor-flexible') ) {
				ob_start();
					// loop through the rows of data
				while ( have_rows('mj-visual-editor-flexible') ) : the_row();
					$this->renderBlockTemplates();
				endwhile;
				$content = ob_get_clean();
			}
		}
		return $content;
	}

	/**
	 * Render all Blocks which are used inside the current post
	 *
	 * @return false|string
	 * @throws \Exception
	 */
	public function renderBlockTemplates() {
		foreach($this->getBlocks() as $block) {
			if ( is_layout( $block->getTemplateName() ) ) {
				$block->render();
			}
		}
	}

	public function isEnabled() {
		return $this->_enabled;
	}

	public function getBlocks() {
		return apply_filters('mj/frontend/visual-editor/set_layout_blocks', $this->_layouts);
	}
	/**
	 *
	 */
	public function getLayoutFields() {
		$layouts = $this->getBlocks();
		$l = [];
		foreach($layouts as $layout) {
			$l[] = $layout->getActLayouts();
		}
		return $l;
	}

	/**
	 * These are the default locations we're the Page Builder will be shown
	 * You can easily change it with the set_location filter
	 *
	 * @return array
	 */
	public function getLocations() {
		return apply_filters('symbiotic/acf_page_builder/set_location', [
			[
				acf_location( 'post_type', 'page' ),
			],
			[
				acf_location( 'post_type', 'post' ),
			],
		]);
	}


	/**
	 * Disable WYSIWYG Editor in Wordpress
	 *
	 */
	public function removeEditorSupport() {
		$post_types = apply_filers('symbiotic/acf_page_builder/set_location', [
			'page', 'post'
		]);

		if(!empty($post_types) && is_array($post_types)) {
			foreach ( $post_types as $pt ) {
				remove_post_type_support( $pt, 'editor' );
			}
		}
	}

//
//	protected function include_template($template_name, $params = array()) {
//		//ob_start();
//		$template_file = locate_template($this->getTemplatePath() . $template_name .'.php', false, false);
//		var_dump($this->getTemplatePath() . $template_name .'.php');
//		exit;
//		extract($params, EXTR_SKIP);
//		if ($template_file) include( $template_file );
//		return ob_get_clean();
//	}

	public function enable() {
		$this->registerEditorFields();
		$this->_enabled = true;
	}

	public function disable() {
		$this->_enabled = false;
	}
//
//	public function getTemplatePath() {
//		return apply_filters('symbiotic/acf_page_builder/set_template_path', trailingslashit(get_template_directory() . '/template-parts'));
//	}
}