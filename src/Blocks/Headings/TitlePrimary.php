<?php

namespace Coretik\PageBuilder\Blocks\Headings;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Blocks\Block;

use function Globalis\WP\Cubi\include_template_part;

class TitlePrimary extends Block
{
    const NAME = 'headings.title-primary';
    const LABEL = 'Titre principal';

    protected $title;
    protected $subtitle;

    public function flexibleLayoutArgs(): array
    {
        return [
            'max' => 1,
            'min' => 0,
        ];
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $field = new FieldsBuilder(static::NAME, $this->fieldsBuilderConfig());
        $field
            ->addMessage(__("Titre principal", 'cfg'), __("Afficher le titre"))
            ->addText('subtitle')
                ->setLabel(__('Sous-titre', app()->get('settings')['text-domain']));
        return $field;
    }

    public function toArray()
    {
        if (empty($this->title) && !empty(\acfe_get_post_id())) {
            $current = \acf_decode_post_id(\acfe_get_post_id());

            switch ($current['type']) {
                case 'post':
                    $selector = get_post_type($current['id']);
                    $type = 'post';
                    break;
                case 'term':
                    $term = \get_term($current['id']);
                    $selector = $term->taxonomy;
                    $type = 'taxonomy';
                    break;
                case 'comment':
                    $selector = 'comments';
                    $type = 'comment';
                    break;
                case 'user':
                    $selector = 'users';
                    $type = 'user';
                    break;
            }

            if (!empty($selector) && !empty($type)) {
                $this->title = app()
                                ->schema()
                                ->get($selector, $type)
                                ?->model((int)$current['id'])
                                ?->title();
            }
        }

        return [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
        ];
    }
}
