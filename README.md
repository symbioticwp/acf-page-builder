# Advanced Custom Fields (ACF) Page Builder
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

Create your custom 'Blocks' which you would like to use in your Page Builder. 
A Block can be for example a Slider, Image Gallery or a simple Text Block.
 
Add your Blocks: functions.php
```php
<?php

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
});

```

For complex logic or templates you can create your own Block Class which derives from
AbstractBlock. You need to implement a `registerLayout` and a `render` method.

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

Register the TemplateBlock in your Page Builder.
```php
<?php

use App\Blocks\TemplateBlock;

add_action('symbiotic/acf_page_builder/register_page_blocks', function($pageBuilder) {

    // Initialize Blocks with a class (for complex logic)
	$pageBuilder->addLayout(new TemplateBlock());
});
```

You can also make use of hooks where you adjust your configuration.
For example lets add a Settings Tab to every existing Block.

```php
<?php
/**
 * Add a settings Tab to every Block
 */
 
add_action('symbiotic/acf_page_builder/add_layout_after', function($layoutConfig) {
	if(!isset($layoutConfig['sub_fields'])) {
		var_dump($layoutConfig);
	}
	$layoutConfig['sub_fields'] = array_merge(
		$layoutConfig['sub_fields'],
		addSettingsTab()
	);

	return $layoutConfig;
});

/**
 *
 * @return array
 */
function addSettingsTab() {
	return [
		acf_tab(['label' => 'Settings']),
		acf_text([ 'label' => 'Container Class',
		            'name' => 've-settings-container',
					'instructions' => 'You can define your own container classes which gets included in the Html Block Wrapper'])
	];
}
```

You can use the `set_location` hook to customize where you want to display
your Page Builder. For Example lets enable the Page Builder also for `portfolio` 
and `services` post types.

```php
<?php
add_action('symbiotic/acf_page_builder/set_location', function($location) {
	$location[] = [acf_location('post_type', 'portfolio')];
	$location[] = [acf_location('post_type', 'services')];
	return $location;
});
```

### Additional Info

* 'Blocks' are comparable to components in react or widgets in Elementor/Wordpress.


### Roadmap


* Finish documentation


### Changelog

##### 1.0.0

* Initial Build