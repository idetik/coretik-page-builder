<?php

namespace Coretik\PageBuilder\Cli\Command\SubCommand;

use Coretik\PageBuilder\Core\Job\Block\BlockType;

trait CreateSubCommand
{
    /**
     * Create block
     *
     * ## OPTIONS
     *
     * [<class>]
     * : The block classname
     *
     * [--type=<block_type>]
     * : The block type
     * ---
     * default: block
     * options:
     *   - block
     *   - component
     *   - composite
     * ---
     *
     * [--name=<name>]
     * : The block name to retrieve template (ex: component.title, template based in blocks/component/title.php)
     *
     * [--label=<label>]
     * : The block title
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
     *     wp page-builder create Component/MyComponent --name=component.my-component --type=component --label="My super Component" --force
     */
    public function create($args, $assoc_args)
    {
        $class = \rtrim($args[0], '.php');
        $verbose = array_key_exists('quiet', $assoc_args) ? false : true;
        $name = $assoc_args['name'] ?? null;
        $label = $assoc_args['label'] ?? null;
        $type = $assoc_args['type'] ?? false;
        $force = $assoc_args['force'] ?? false;

        if (array_key_exists('w', $assoc_args)) {
            $createTemplateFile = false;
            $createAcfAdminFiles = false;
        } else {
            $createTemplateFile = array_key_exists('without-template-file', $assoc_args) ? false : true;
            $createAcfAdminFiles = array_key_exists('without-acf-admin-files', $assoc_args) ? false : true;
        }

        $this->blockJob->setConfig([
            'class' => $class,
            'force' => $force,
            'verbose' => $verbose,
            'name' => $name,
            'label' => $label,
            'createTemplateFile' => $createTemplateFile,
            'createAcfAdminFiles' => $createAcfAdminFiles,
        ])->setBlockType(match ($type) {
            'component' => BlockType::Component,
            'block' => BlockType::Block,
            'composite' => BlockType::Composite,
            default => BlockType::Block,
        })->handle();
    }
}
