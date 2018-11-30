# Advanced Custom Feilds (ACF) Page Builder
ACF Page Builder using [Wordplate ACF](https://github.com/wordplate/acf) and [ACF Flexible Content Field](http://www.advancedcustomfields.com/add-ons/flexible-content-field/)

### Requirements

❗ **Note:** Advanced Custom Fields Pro ([Flexible Content Field](http://www.advancedcustomfields.com/add-ons/flexible-content-field/)) is required to use this package

### Installation

#### Composer

```shell
$ composer require symbioticwp/acf-page-builder
```

#### Requirements

* [PHP](http://php.net/manual/en/install.php) >= 7.0

### Setup

Enable dynamic class include ([PSR-4 autoloading](https://www.php-fig.org/psr/psr-4/))
via composer autoloader. (Hint: Check first if you don't already have the following 
snippet in your theme)

````php
<?php
if (file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    require_once $composer;
}
````

### How-To


Custom Block: app/Blocks/TemplateBlock.php
```php
<?php
namespace App\Blocks;

use Symbiotic\AcfPageBuilder\Blocks\AbstractBlock;

class TemplateBlock extends AbstractBlock {

	protected function registerLayout() {
		return [
			'label'      => 'Template Block',
			'name'       => 've-template-block',
			'display'    => 'block',
			'sub_fields' => [
				acf_tab(['label' => 'Details']),
				acf_text( [ 'label' => 'Path to Template File', 'name' => 've-template-filename' ] ),
			]
		];
	}

	public function render() {
		$this->includeTemplate(field('ve-template-filename'));
	}
}
```

Add your Blocks: functions.php
```php
<?php

use App\Blocks\TemplateBlock;

add_action('symbiotic/acf_page_builder/register_page_blocks', function($pageBuilder) {


    // Create Blocks directly with an array
	$pageBuilder->addLayout([
		'label'      => 'Text Block',
		'name'       => 've-text-block',
		'display'    => 'block',
		'sub_fields' => [
			acf_tab(['label' => 'Details']),
			acf_text( [ 'label' => 'Title', 'name' => 've-text-title' ] ),
			acf_wysiwyg( [ 'label' => 'Content', 'name' => 've-text-content' ] ),
		]
	]);

	$pageBuilder->addLayout([
		'label'      => 'Image Block',
		'name'       => 've-image-block',
		'display'    => 'block',
		'sub_fields' => [
			acf_tab(['label' => 'Details']),
			acf_image( [ 'label' => 'Image', 'name' => 've-image-block' ] ),
			acf_text( [ 'label' => 'Aspect Ratio', 'name' => 've-aspect-ratio', 'default_value' => '16:9' ] ),
		]
	]);

	$pageBuilder->addLayout([
		'label'      => 'Slider Block',
		'name'       => 've-slider-block',
		'display'    => 'block',
		'sub_fields' => [
			acf_tab(['label' => 'Details']),
			acf_text( [ 'label' => 'Title', 'name' => 've-slider-title' ] ),
			acf_text( [ 'label' => 'Aspect Ratio', 'name' => 've-slider-aspect-ratio', 'default_value' => '16:9' ] ),
			acf_gallery([
				'name' => 've-gallery-block',
				'label' => 'Images',
				'instructions' => 'Add the gallery images.',
				'required' => true,
				'mime_types' => 'jpeg, jpg, png',
				'min' => 1,
			]),
		],
	]);

	$pageBuilder->addLayout([
		'label'      => 'Hero Block',
		'name'       => 've-hero-block',
		'display'    => 'block',
		'sub_fields' => [
			acf_tab(['label' => 'Details']),
			acf_textarea( [ 'label' => 'Title', 'name' => 've-hero-title' ] ),
		],
	]);

	$pageBuilder->addLayout([
		'label'      => 'Client Block',
		'name'       => 've-client-block',
		'display'    => 'block',
		'sub_fields' => [],
	]);

	$pageBuilder->addLayout([
		'label'      => 'Content Block',
		'name'       => 've-content-block',
		'display'    => 'block',
		'sub_fields' => [
			acf_tab(['label' => 'Details']),
			acf_text( [ 'label' => 'Title', 'name' => 've-content-title' ] ),
			acf_text( [ 'label' => 'Sub Title', 'name' => 've-content-subtitle' ] ),
			acf_wysiwyg( [ 'label' => 'Content', 'name' => 've-content-text' ] ),
		]
	]);

    // Initialize Blocks with a class (for complex logic)
	$pageBuilder->addLayout(new TemplateBlock());
});

/**
 * Add a settings Tab to every Block
 */
add_action('symbiotic/acf_page_builder/add_layout_after', function($layoutConfig) {
	if(!isset($layoutConfig['sub_fields'])) {
		var_dump($layoutConfig);
	}
	$layoutConfig['sub_fields'] = array_merge(
		$layoutConfig['sub_fields'],
		generateSettingsTab()
	);

	return $layoutConfig;
});

/**
 *
 * @return array
 */
function generateSettingsTab() {
	return [
		acf_tab(['label' => 'Settings']),
		acf_text([ 'label' => 'Container Class',
		            'name' => 've-settings-container',
					'instructions' => 'You can define your own container classes which gets included in the Html Block Wrapper']),
		acf_text([ 'label' => 'data-node-type',
		            'name' => 've-settings-data-node-type',
		            'instructions' => 'Used for JS: The node Type is generated based on the block name. For example SliderBlock. Anyway you can overwrite your Block Name here.' ]),

		// For every row there will be a unique id="<id>" generated.
		// We save it here in this <hidden> field
		acf_randomstring(['label' => 'row-id','name' => 'row-id'])
	];
}


/** ======================================================
 *  For which post Types you want to display your Page Builder?
 *  See also AcfPageBuilder getLocations() for default locations
 *
 * Examples:
 * $location[] = [acf_location('post_type', 'portfolio')]; // OR OPERATOR
 *  ======================================================
 */
add_action('symbiotic/acf_page_builder/set_location', function($location) {
	$location[] = [acf_location('post_type', 'portfolio')];
	$location[] = [acf_location('post_type', 'services')];
	return $location;
});
````


### Roadmap


* Finish documentation


### Changelog

##### 1.0.0

* Initial Build