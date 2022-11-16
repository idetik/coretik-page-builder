<?php

namespace Coretik\PageBuilder\Blocks\Content;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;
use Coretik\PageBuilder\BlockInterface;
use Coretik\PageBuilder\Blocks\Traits\{WithComponent};

use function Globalis\WP\Cubi\include_template_part;

class WysiwygDouble extends Block
{
    use WithComponent;

    const NAME = 'content.wysiwyg-double';
    const LABEL = 'Contenu: Éditeur de texte (2 colonnes)';

    protected $content_1;
    protected $content_2;

    public function fieldsBuilder(): FieldsBuilder
    {
        $wysiwyg_fields = $this->component('components.wysiwyg');

        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $field
        ->addField('col1', 'acfe_column', ['columns' => '6/12', 'label' => '(Column 6/12)'])
            ->addFields(
                $wysiwyg_fields
                    ->fieldsBuilder('content_1')
                    ->modifyField('content_1', ['default_value' => '<h2>Lorem Ipsum</h2><h3>Dolor it</h3><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p><h3>Dolor it</h3><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>'])
            )
        ->addField('col2', 'acfe_column', ['columns' => '6/12', 'label' => '(Column 6/12)'])
            ->addFields(
                $wysiwyg_fields
                    ->fieldsBuilder('content_2')
                    ->modifyField('content_2', ['default_value' => '&nbsp;<h3>Dolor it</h3><ul><li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li><li>Aliquam tincidunt mauris eu risus.</li><li>Vestibulum auctor dapibus neque.</li></ul><p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, commodo vitae, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. Donec non enim in turpis pulvinar facilisis. Ut felis. Praesent dapibus, neque id cursus faucibus, tortor neque egestas augue, eu vulputate magna eros eu erat. Aliquam erat volutpat. Nam dui mi, tincidunt quis, accumsan porttitor, facilisis luctus, metus</p>'])
            )
        ->addField('col3', 'acfe_column', ['columns' => '12/12', 'label' => '(Column 12/12)']);
        $this->useSettingsOn($field);
        return $field;
    }

    public function toArray()
    {
        return [
            'wysiwyg_1' => $this->component('components.wysiwyg')->setProps(['content' => $this->content_1])->toArray(),
            'wysiwyg_2' => $this->component('components.wysiwyg')->setProps(['content' => $this->content_2])->toArray(),
        ];
    }
}
