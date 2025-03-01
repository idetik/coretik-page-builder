[![Latest Stable Version](https://poser.pugx.org/idetik/coretik-page-builder/v)](https://packagist.org/packages/idetik/coretik-page-builder) [![License](https://poser.pugx.org/idetik/coretik-page-builder/license)](https://github.com/idetik/coretik-page-builder/blob/master/LICENSE.md)
# Pagebuilder with ACF for Coretik

Coretik page builder provides a modern way for developers to build blocks for page builder with **live admin preview** (and it is WP-CLI friendly !).
It uses framework logic containing reusable components and composite blocks and it manages as many components levels as necessary. It also provides a way to build block types for the block editor (Gutenberg) with the same logic.

Coding
----------
![004 (3)](https://github.com/user-attachments/assets/b595cc34-c3c6-4b1b-bdbc-55aa6738f010)

Editing | Rendering
--|--
![004 (2)](https://github.com/user-attachments/assets/b90ba875-86e6-4bcf-9004-c781c30a8fbc) | ![004](https://github.com/user-attachments/assets/9ed2f8da-73af-4de1-987c-ffb60fd3f6c9)


## Overview
This package works with [StoutLogic/acf-builder](https://github.com/StoutLogic/acf-builder) to create fields and just provide a way to build `StoutLogic\AcfBuilder\FieldsBuilder` blocks. You have to include them in any other fields you want.

Block instance defined all its features :
- rendering method ;
- admin fields and admin preview ;
- block type to register a block.json.

Three block levels exist :
- **Components** : the lower block level, used to build other blocks ;
- **Block** : a free way to build a complete block instance ;
- **Composite** : a fast way to build blocks based on many components or others blocks ;

Components doesn't appear in the user library. There are only used to build others blocks.

## Requirements
- PHP >= 8.0
- `idetik/coretik` : https://github.com/idetik/coretik
- Plugin ACF : https://www.advancedcustomfields.com/ (PRO recommended)
- Plugin ACF Extended : https://acf-extended.com/

## Installation

`composer require idetik/coretik-page-builder`

## Usage

This builder comes with some components ready to use. You can find them in `./src/Library/Component/`

### Configuration
First, you should define your own environment variables to ovveride the default settings.
The default config list :
```php
// The template directory in your theme to save the html views parts
'blocks.template.directory' => 'templates/blocks/',

// The blocks classes directory
'blocks.src.directory' => 'src/Services/PageBuilder/Blocks/',

// The template acf directory in your theme to save the additional admin styles and scripts
'blocks.acf.directory' => 'templates/acf/',

// Your blocks root namespace based on your app
'blocks.rootNamespace' => ($c['rootNamespace'] ?? 'App') . '\\Services\\PageBuilder\\Blocks',

// The blocks library containing all of your blocks
'blocks' => $c->get('pageBuilder.library')

// The fields directory to create and write complex fields groups
'fields.directory' => 'src/admin/fields/blocks/',

// The block thumbnail directory to save the blocks previews thumbnails
'fields.thumbnails.directory' => \get_stylesheet_directory() . '/assets/images/admin/acf/',

// The block thumbnail url to get the blocks previews thumbnails
'fields.thumbnails.baseUrl' => '<##ASSETS_URL##>/images/admin/acf/',
```


```php
// Example to change the template directory
add_filter('coretik/page-builder/config', function ($config) {
    $config['blocks.template.directory'] = 'my-theme-templates/';
    return $config;
});
```

### Blocks classes

**Every block and composite have the capability to build a block.json** file to register a block type in the block editor (Gutenberg) simply by implementing the `Coretik\PageBuilder\Core\Contract\ShouldBuildBlockType` interface. You can use the `Coretik\PageBuilder\Core\Block\Traits\BlockType` trait to do it.

#### Component
This lower block level allow you to reuse some basics fields in your theme. Simplified example for the titleComponent provided in this package as ready to use.
```php
<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use Coretik\PageBuilder\Core\Contract\ShouldBuildBlockType;
use Coretik\PageBuilder\Core\Block\Traits\BlockType;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TitleComponent extends BlockComponent implements ShouldBuildBlockType
{
    use BlockType; // Use this trait to build a block.json file

    const NAME = 'component.title';
    const LABEL = 'My title'; // Admin label

    /**
     * All fields to be retrieved must be declared as property class.
     * There are automatically populated with values from the database.
     */
    protected $title;
    protected $tag;

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field
            // First field : 'title'
            ->addText('title')
                ->setLabel('Title')
                ->setRequired()
            // Second field : 'tag'
            ->addRadio('tag', ['layout' => 'horizontal'])
                ->setLabel('Title level')
                ->addChoice('h2')
                ->addChoice('h3')
                ->addChoice('h4')
                ->addChoice('h5')
                ->setDefaultValue('h2')
                ->setRequired();

        $this->useSettingsOn($field);
        return $field;
    }

    /**
    * The block formatted data. You can apply your own format rules.
    */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'tag' => $this->tag
        ];
    }

    /**
    * This is usefull to get html from light component without template file
    */
    protected function getPlainHtml(array $parameters): string
    {
        // Return <hN>A title from my acf field</hN>
        return sprintf(
            '<%1$s class="my_title_class">%2$s</%1$s>',
            $parameters['tag'],
            $parameters['title']
        );
    }
}
```
#### Block
Blocks use the same logic than the components except there are available directly in the block library. A block can reuse components with the php trait `Coretik\PageBuilder\Core\Block\Traits\Components`
```php
use Components;

$titleComponent = $this->component('component.title'); // Or with some defined values : $this->component(['acf_fc_layout' => 'component.title', 'tag' => 'h2'])
$titleFields = $titleComponent->fields();
[...]
```
#### Composite
Compose block based on others blocks or components. Simply create a complex block. This example shows you a way to create a title and wysiwyg editor block.
```php
<?php

namespace Coretik\PageBuilder\Library\Block;

use Coretik\PageBuilder\Core\Block\BlockComposite;
use Coretik\PageBuilder\Library\{
    Component\TitleComponent,
    Component\WysiwygComponent,
};

use function Coretik\PageBuilder\Core\Block\Modifier\required;

class TitleAndTextComposite extends BlockComposite
{
    const NAME = 'block.title-editor';
    const LABEL = 'Title and editor';

    // By default, the rendering method is HTML. Other methods are RenderingType::Array or RenderingType::Object
    const RENDER_COMPONENTS = RenderingType::Html;

    /**
     * Define all sub blocks to build in this composite block.
     * The acf fields will be constructed and rendered from each component.
     */
    protected function prepareComponents(): array
    {
        return [
            'title' => TitleComponent::class,
            /**
             * Some modifiers functions are available to help you to build specifics composite blocks.
             * See `/src/Core/Block/Modifier/modifiers.php` 
             */
            'editor' => required(WysiwygComponent::class), // required(Block::class) set acf field as required
        ];
    }

    // Or use template in your template.directory/block/title-editor.php
    protected function getPlainHtml(array $parameters): string
    {
        /**
         * Following the RenderingType, the values will change. By default, values are the html rendered by each of sub blocks
         * RenderingType::Array allow you to get values from toArray method of each sub blocks
         * RenderingType::Object allow you to get sub blocks instances
         */
        return sprintf('%s%s', $parameters['title'], $parameters['editor']);
    }
}
```
Template example `template.directory/block/title-editor.php`
```html
<div class="title-with-editor-block">
    <div class="title-with-editor-block__title">
        <?= $title ?>
    </div>
    <div class="title-with-editor-block__editor">
        <?= $editor ?>
    </div>
</div>
```
### WP-CLI : Create Blocks
These commands will create the PHP files to create and render a block or component.
#### Create component
`wp page-builder create-component "My super component"`
#### Create composite
`wp page-builder create-composite "My super block composite"`
#### Create blocks
`wp page-builder create-block "My super block"`

---
Options availables for each commands:
```
[--name=<name>] : The block name to retrieve template (ex: component.title, template based in blocks/component/title.php), default is component.<labelToCamelCase>

[--class=<class>] : The block classname, default is (Component/<labelToCamelCase>Component)

[--without-acf-admin-files] : Avoid to create ACF admin files (script, style and template)

[--without-template-file] : Avoid to create template file

[--w] : Shortcut to create only class file

[--quiet] : Disable output

[--force]: Override existings files
```

### Include it in your `FieldsBuilder`

```php
// Get the flexible content instance
$pageBuilder = app()->get('pageBuilder.field');

// You can filter ou use some specifics blocks as necessary
$pageBuilder->setBlocks(app()->get('pageBuilder.library')->filter());

// Generate the FieldsBuilder with your $fieldName, example "blocks"
$pageBuilder = $pageBuilder->field('blocks');

$page = new FieldsBuilder('page_builder', [
    'title' => 'Builder',
    'acfe_autosync' => ['php'],
    'hide_on_screen' => [
        'the_content',
        'custom_fields',
    ],
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label'
]);

$page
    ->addFields($pageBuilder)
    ->setLocation('post_type', '==', 'page');
```
