<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;


class RequiredModifier
{
    private static $instance;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function modify(BlockInterface|string $block)
    {
        if (is_string($block)) {
            $block = app()->get('pageBuilder.factory')->create($block::NAME);
        }
        $block->addModifier(static::getInstance());
        // var_dump($block->fields());
        // die;
        // foreach ($block->fields()->getFields() as &$field) {
        //     $field->setRequired();
        // }

        return $block;
    }

    public function __invoke(FieldsBuilder $fields, BlockInterface $block)
    {
        // var_dump($fields->getFields());
        // die;
         foreach ($fields->getFields() as $field) {
            $field->setRequired();
        }

        // var_dump($field);
        // die;
        return $fields;
        // var_dump($fields->getFields());
        // die;
    }
}
