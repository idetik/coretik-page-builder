<?php

namespace Coretik\PageBuilder\Blocks\Traits;

trait Faker
{
    protected $propsFake = [];

    public function fakeIt(): self
    {
        $props = \apply_filters('coretik/page-builder/fake-it/name=' . $this->getName(), $this->propsFake ?? []);

        $build = $this->fields()->build();
        foreach ($build['fields'] as $field) {
            if (!isset($props[$field['name']])) {
                $props[$field['name']] = static::fakeField($field);
            }
        }

        $this->setProps($props);
        return $this;
    }

    protected static function fakeField($field): mixed
    {
        if (!empty($field['default_value'])) {
            return $field['default_value'];
        }

        $fakeField = \apply_filters('coretik/page-builder/block/fake-field', '', $field['type'], $field);
        if (!empty($fakeField)) {
            return $fakeField;
        }

        switch ($field['type']) {
            case 'button_group':
            case 'radio':
            case 'select':
                $choices = array_keys($field['choices']);
                if (empty($choices)) {
                    return '';
                }
                $random = array_rand($choices);
                return $choices[$random];
            case 'checkbox':
                $choices = array_keys($field['choices']);
                if (empty($choices)) {
                    return [];
                }
                $random = array_rand($choices);
                return [$choices[$random]];
            case 'repeater':
            case 'group':
                $fakeChildren = function () use ($field) {
                    $subfields = [];
                    foreach ($field['sub_fields'] as $subfield) {
                        $subfields[$subfield['name']] = static::fakeField($subfield);
                    }
                    return $subfields;
                };

                if ('repeater' === $field['type']) {
                    $subfields = [];
                    $min = $field['min'] ?? 0;
                    $max = $field['max'] ?? ($min + 3);
                    $average = ceil(($min + $max) / 2);
                    $counter = $average > 1 ? $average : ($min + 2);
                    for ($i = 0; $i < $counter; $i++) {
                        $subfields[] = $fakeChildren();
                    }
                    return $subfields;
                }

                return $fakeChildren();
            case 'relationship':
                $min = $field['min'] ?? 0;
                $max = $field['max'] ?? ($min + 3);
                $average = ceil(($min + $max) / 2);

                switch ($field['return_format']) {
                    case 'ids':
                        return \array_map(function () {
                            return app()->faker()->postId();
                        }, range(0, $average));
                    default:
                        return \array_map(function () {
                            return app()->faker()->post();
                        }, range(0, $average));
                }
            case 'gallery':
                return [
                    app()->faker()->attachmentId(),
                    app()->faker()->attachmentId(),
                    app()->faker()->attachmentId(),
                ];
            case 'image':
                return app()->faker()->attachmentId();
            case 'text':
                return app()->faker()->text(20);
            case 'textarea':
                return app()->faker()->text(50);
            case 'wysiwyg':
                return implode('', \array_map(function ($p) {
                    return sprintf('<p>%s</p>', $p);
                }, app()->faker()->paragraphs(5)));
            case 'page_link':
                return app()->faker()->pageId();
            case 'true_false':
                return rand(0, 1);
            case 'number':
                return rand($field['min'] ?? 0, $field['max'] ?? 100);
            case 'date_time_picker':
                return app()->faker()->date('Y-m-d H:i:s');
            default:
                return '';
        }
    }
}
