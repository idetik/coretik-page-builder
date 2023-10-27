<?php

namespace Coretik\PageBuilder\Blocks\Component;

use Coretik\PageBuilder\Blocks\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class WysiwygComponent extends BlockComponent
{
    const NAME = 'components.wysiwyg';
    const LABEL = 'Éditeur de texte';
    const SCREEN_PREVIEW_SIZE = [1600, 724];

    protected $content;

    protected $fakeContent = '<h2>HTML Ipsum Presents</h2><p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p><h3>Header Level 2</h3><ol><li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li><li>Aliquam tincidunt mauris eu risus.</li></ol><blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote><h4>Header Level 3</h4><ul><li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li><li>Aliquam tincidunt mauris eu risus.</li></ul>';

    public function fieldsBuilder(): FieldsBuilder
    {
        $this->propsFake['content'] = $this->fakeContent;

        $field = new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
        $field->addWysiwyg('content', ['media_upload' => 1])
            ->setLabel(__('Contenu', app()->get('settings')['text-domain']));
        // $field = $this->applyModifiers($field);
        return $field;
    }

    public function toArray()
    {
        return [
            'wysiwyg' => $this->content
        ];
    }
}
