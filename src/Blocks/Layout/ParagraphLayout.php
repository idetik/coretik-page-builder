<?php

namespace Coretik\PageBuilder\Blocks\Layout;

use Coretik\PageBuilder\Blocks\{
    BlockComposite,
    Component\TitleComponent,
    Block\Editorial\WysiwygBlock,
};
use Coretik\PageBuilder\Blocks\Component\ImageComponent;
use Coretik\PageBuilder\Blocks\Component\LinkComponent;
use Coretik\PageBuilder\Blocks\Modifier\PersistantIdModifier;

use function Coretik\PageBuilder\Blocks\Modifier\required;
use function Coretik\PageBuilder\Blocks\Modifier\tabless;

class ParagraphLayout extends BlockComposite
{
    const NAME = 'layouts.paragraph';
    const LABEL = 'Titre + Paragraphe';

    protected function prepareComponents(): array
    {
        $this->addModifier([PersistantIdModifier::make(), 'handle'], 1);
        return [
            'title' => TitleComponent::class,
            'text' => required(tabless(WysiwygBlock::class)),
            'cta' => LinkComponent::class,
            'image' => ImageComponent::class,
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return sprintf('%s%s', $parameters['title'], $parameters['text']);
    }
}
