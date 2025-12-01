<?php

namespace Coretik\PageBuilder\Core\Block\Modifier;

use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;

class AcfmlModifier extends Modifier
{
    const NAME = 'acfml';
    const PRIORITY = 90;

    /**
     * @param FieldsBuilder $fields
     * @param BlockInterface $block
     * @return FieldsBuilder
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        foreach ($fields->getFields() as $field) {

            $fieldConfig = $field->getConfig();
            $bypass = array_key_exists('wpml_cf_preferences', $fieldConfig) ? $fieldConfig['wpml_cf_preferences'] : false;
            $bypass = apply_filters('coretik/page-builder/modifier/acfml/bypass-field', $bypass, $field, $block);
            $bypass = apply_filters('coretik/page-builder/modifier/acfml/bypass-field/name=' . $field->getName(), $bypass, $field, $block);
            if ($bypass) {
                continue;
            }

            $strategy = apply_filters('coretik/page-builder/modifier/acfml/translation-strategy', null, $field, $block);
            $strategy = apply_filters('coretik/page-builder/modifier/acfml/translation-strategy/name=' . $field->getName(), $strategy, $field, $block);

            if (!isset($strategy)) {
                $config = $field->build();
                $strategy = $this->getTranslationStrategy($config['type']);
            }

            $field->setConfig('wpml_cf_preferences', $strategy);
        }

        return $fields;
    }

    private function getTranslationStrategy(string $type): int
    {
        $defaultStrategy = apply_filters(
            'coretik/page-builder/modifier/acfml/default-translation-strategy',
            defined('WPML_COPY_CUSTOM_FIELD') ? WPML_COPY_CUSTOM_FIELD : 1,
            $type
        );

        $needTranslate = apply_filters(
            'coretik/page-builder/modifier/acfml/need-translate-fields',
            ['text', 'textarea', 'wysiwyg', 'message', 'acfe_advanced_link'],
            $type
        );

        $typeStrategy = apply_filters(
            'coretik/page-builder/modifier/acfml/type-translation-strategy/' . $type,
            in_array($type, $needTranslate) ? (defined('WPML_TRANSLATE_CUSTOM_FIELD') ? WPML_TRANSLATE_CUSTOM_FIELD : 2) : $defaultStrategy,
        );

        return $typeStrategy;
    }
}
