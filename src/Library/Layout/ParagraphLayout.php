<?php

namespace Coretik\PageBuilder\Library\Layout;

use Coretik\PageBuilder\Core\Block\BlockComposite;
use Coretik\PageBuilder\Library\{
    Component\TitleComponent,
    Component\WysiwygComponent,
};
use Coretik\PageBuilder\Library\Component\ImageComponent;
use Coretik\PageBuilder\Library\Component\LinkComponent;
use Coretik\PageBuilder\Core\Block\Modifier\PersistantIdModifier;

use function Coretik\PageBuilder\Core\Block\Modifier\required;

class ParagraphLayout extends BlockComposite
{
    const NAME = 'layouts.paragraph';
    const LABEL = 'Titre + Paragraphe';

    protected function prepareComponents(): array
    {
        PersistantIdModifier::modify($this);
        return [
            'title' => TitleComponent::class,
            'text' => required(WysiwygComponent::class),
            'cta' => LinkComponent::class,
            'image' => ImageComponent::class,
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return sprintf('%s%s', $parameters['title'], $parameters['text']);
    }
}
