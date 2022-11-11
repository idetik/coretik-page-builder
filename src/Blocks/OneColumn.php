<?php

namespace Themetik\Services\PageBuilder\Blocks;

use Themetik\Services\PageBuilder\BlockInterface;

use function Globalis\WP\Cubi\include_template_part;

class OneColumn implements BlockInterface
{
    const TEMPLATE = "templates/page-builder/blocks/one-column";

    protected $column_title;
    protected $column_content;

    /**
     * $column_content string | BlockInterface
     */
    public function __construct(string $column_title, $column_content)
    {
        $this->column_title = $column_title;
        $this->column_content = $column_content;
    }

    public function toArray()
    {
        return [
            'title' => $this->column_title,
            'content' => $this->column_content instanceof BlockInterface ? $this->column_content->render() : $this->column_content,
        ];
    }

    public function render()
    {
        return include_template_part(static::TEMPLATE, $this->toArray(), true);
    }
}
