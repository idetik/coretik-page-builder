<?php

namespace Coretik\PageBuilder\Blocks\Block\Editorial;

use Coretik\PageBuilder\Blocks\BlockComposite;
use Coretik\PageBuilder\Blocks\Component\WysiwygComponent;

use Coretik\PageBuilder\Blocks\Modifier\PersistantIdModifier;
use function Coretik\PageBuilder\Blocks\Modifier\required;

class WysiwygBlock extends BlockComposite
{
    const NAME = 'editorial.blockwysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected function prepareComponents(): array
    {
        return [
            'wysiwyg' => required(WysiwygComponent::class)
        ];
    }

    protected function getPlainHtml(array $parameters): string
    {
        return $parameters['wysiwyg'] ?? '';
    }
}
