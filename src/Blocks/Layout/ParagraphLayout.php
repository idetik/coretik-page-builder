<?php

namespace Coretik\PageBuilder\Blocks\Layout;

use Coretik\PageBuilder\Blocks\{
    BlockComposite,
    Component\TitleComponent,
    Block\Editorial\WysiwygBlock,
};

use function Coretik\PageBuilder\Blocks\Modifier\required;
use function Coretik\PageBuilder\Blocks\Modifier\tabless;

class ParagraphLayout extends BlockComposite
{
    const NAME = 'layouts.paragraph';
    const LABEL = 'Titre + Paragraphe';

    protected $components;

    protected function prepareComponents(): void
    {
        $this->components = [
            'title' => TitleComponent::class ,
            'text' => required(tabless(WysiwygBlock::class)),
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return sprintf('%s%s', $parameters['title'], $parameters['text']);
    }
}
