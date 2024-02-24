<?php

namespace Coretik\PageBuilder\Cli\Command\SubCommand;

use Coretik\PageBuilder\Core\Job\Block\BlockType;

trait CreateComponentSubCommand
{
    /**
     * Create component
     *
     * @subcommand create-component
     *
     * ## OPTIONS
     *
     * [<label>]
     * : The block label
     *
     * [--name=<name>]
     * : The block name to retrieve template (ex: component.title, template based in blocks/component/title.php), default is component.<labelToCamelCase>
     *
     * [--class=<class>]
     * : The block classname, default is (Component/<labelToCamelCase>Component)
     *
     * [--without-acf-admin-files]
     * : Avoid to create ACF admin files (script, style and template)
     *
     * [--without-template-file]
     * : Avoid to create template file
     *
     * [--w]
     * : Shortcut to create only class file
     *
     * [--quiet]
     * : Disable output
     *
     * [--force]
     * : Override existings files
     *
     * ## EXAMPLES
     *
     *     wp page-builder create-component "My super component"
     */
    public function create_component($args, $assoc_args)
    {
        $label = $args[0];
        $name = $assoc_args['name'] ?? sprintf('component.%s', static::strToKebabCase($args[0]));
        $class = $assoc_args['class'] ?? sprintf('Component/%sComponent', static::strToPascalCase($args[0]));

        if (array_key_exists('w', $assoc_args)) {
            $createTemplateFile = false;
            $createAcfAdminFiles = false;
        } else {
            $createTemplateFile = array_key_exists('without-template-file', $assoc_args) ? false : true;
            $createAcfAdminFiles = array_key_exists('without-acf-admin-files', $assoc_args) ? false : true;
        }

        $verbose = array_key_exists('quiet', $assoc_args) ? false : true;
        $force = $assoc_args['force'] ?? false;

        $this->blockJob->setConfig([
            'class' => $class,
            'force' => $force,
            'verbose' => $verbose,
            'name' => $name,
            'label' => $label,
            'createTemplateFile' => $createTemplateFile,
            'createAcfAdminFiles' => $createAcfAdminFiles,
        ])->setBlockType(BlockType::Component)->handle();
    }
}
