<?php

namespace Themetik\Services\PageBuilder\Blocks;

use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class Image implements BlockInterface
{
    const TEMPLATE = "templates/page-builder/blocks/image";

    protected $image_id;
    protected $title;

    public function __construct(int $image_id, string $title = '')
    {
        $this->image_id = $image_id;
        $this->title = $title;
    }

    public function tag(): string
    {
        if (empty($this->image_id)) {
            return '';
        }

        $size_x2 = \wp_get_attachment_image_src($this->image_id, 'fiev-wide-cover-x2')[0];
        $size_x1 = \wp_get_attachment_image_src($this->image_id, 'fiev-wide-cover')[0];
        $size_mobile = \wp_get_attachment_image_src($this->image_id, 'fiev-wide-cover-mobile')[0];
        $image_tag = '<picture><source srcset="' . $size_x1 . ' 1440w, ' . $size_x2 . ' 2880w" sizes="100vw" media="(min-width: 600px)"><source srcset="' . $size_mobile . '" media="(max-width: 600px)"><img src="' . $size_mobile . '"></picture>';
        return $image_tag;
    }

    public function toArray()
    {
        return [
            'image' => $this->tag(),
            'title' => $this->title,
        ];
    }

    public function render()
    {
        return include_template_part(static::TEMPLATE, $this->toArray(), true);
    }
}
