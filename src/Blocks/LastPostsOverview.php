<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;

class LastPostsOverview extends Block
{
    const NAME = 'content.last-posts-overview';
    const LABEL = 'Contenu: Derniers articles';
    const SCREENSHOTABLE = false;

    protected $auto;
    protected $manual;
    protected $post_type;
    protected $title;

    public function posts(): array
    {
        $settings = [];
        $posts = [];
        if (!$this->auto) {
            $settings = $this->manual;
            foreach ($settings['posts'] ?: [] as $post_id) {
                $post_type = get_post_type($post_id);
                $model = app()->schema()->get($post_type)->model($post_id);
                $posts[] = [
                    'permalink' => $model->permalink(),
                    'thumbnail_id' => $model->thumbnailId(),
                    'title' => $model->title(),
                    'category' => method_exists($model, 'category') ? $model->category()->name : '',
                    'excerpt' => $model->excerpt(),
                ];
            }
        } else {
            $post_type = $this->post_type;
            try {
                $mediator = app()->schema()->get($post_type);
                if (!empty($mediator)) {
                    $models = $mediator->query()->limit(3)->models();
                    foreach ($models as $model) {
                        $posts[] = [
                            'permalink' => $model->permalink(),
                            'thumbnail_id' => $model->thumbnailId(),
                            'title' => $model->title(),
                            'category' => method_exists($model, 'category') ? $model->category()->name : '',
                            'excerpt' => $model->excerpt(),
                        ];
                    }
                }
            } catch (\Coretik\Core\Exception\ContainerValueNotFoundException $e) {
                $posts = [];
            }
        }

        return $posts;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'posts' => $this->posts(),
            'cta_label' => $this->auto ? app()->schema()->get($this->post_type, 'post')->args()->get('labels')['view_items'] : $this->manual['label'],
            'cta_url' => $this->auto ? \get_post_type_archive_link($this->post_type) : $this->manual['href'],
        ];
    }

    public function fakeIt()
    {
        $build = $this->fields()->build();
        $props = [];

        foreach ($build['fields'] as $field) {
            if (\in_array($field['name'], ['manual', 'auto'])) {
                continue;
            }
            $props[$field['name']] = static::fakeField($field);
        }
        $props['auto'] = 1;
        $this->setProps($props);

        return $this;
    }
}
