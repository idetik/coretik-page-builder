<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

trait Background
{
    protected $background_customizer;
    protected $background_type;
    protected $background_color;
    protected $background_content_contained;
    protected $background_image;
    protected $background_image_mobile;
    protected $background_full_width;
    protected $overlay_settings;
    protected $background_transition;
    protected $background_transition_reverse;

    protected function initializeBackground()
    {
        if (!\array_key_exists('background_customizer', $this->propsFake)) {
            $this->propsFake = \array_merge($this->propsFake, [
                'background_customizer' => 0
            ]);
        }

        $this->addWrapper([$this, 'backgroundWrap'], 10);
    }

    protected function backgroundWrap($component, $block)
    {
        if (empty($this->backgroundToArray())) {
            return $component;
        }

        return include_template_part(
            'templates/blocks/components/background',
            $this->backgroundToArray() + $this->wrapperParameters() + ['component' => $component],
            true
        );
    }

    protected function backgroundToArray(): array
    {
        if ($this->background_customizer) {
            return [
                'background_customizer' => true,
                'background_type' => ($this->background_type ?? false) ?: 'color',
                'background_image' => $this->background_image,
                'background_image_mobile' => $this->background_image_mobile,
                // @deprecated background_full_width
                'background_full_width' => $this->background_full_width ?? true,
                'overlay' => $this->overlay_settings,
                'background_color' => (($this->background_type ?? false) ?: 'color') === 'color' ? $this->background_color : null,
                'background_content_contained' => !empty($this->background_content_contained) && 'none' !== $this->background_content_contained ? $this->background_content_contained : false,
                'background_transition' => $this->background_transition,
                'background_transition_reverse' => $this->background_transition_reverse,
            ];
        } else {
            return [];
        }
    }
}
