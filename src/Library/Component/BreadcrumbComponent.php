<?php

namespace Coretik\PageBuilder\Library\Component;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\Core\Block\BlockComponent;

class BreadcrumbComponent extends BlockComponent
{
    const NAME = 'component.breadcrumb';
    const LABEL = "Fil d'ariane";

    protected $breadcrumb = null;

    protected function getFakeProps(): array
    {
        return [
            'breadcrumb' => [
                ['url' => '#', 'title' => 'Lorem', 'current' => false],
                ['url' => '#', 'title' => 'Ipsum', 'current' => true],
            ]
        ];
    }

    public function fieldsBuilder($fieldName = 'content'): FieldsBuilder
    {
        $field = $this->createFieldsBuilder();
        $field->addMessage(__("Fil d'ariane", app()->get('settings')['text-domain']), __("Afficher le fil d'ariane", app()->get('settings')['text-domain']));
        $this->useSettingsOn($field);
        return $field;
    }

    public function toArray()
    {
        if (is_admin() && app()->has('navigation')) {
            $this->breadcrumb = app()->navigation()->partsFactory('page')->setId(\acfe_get_post_id())->setCurrent()->breadcrumb()->map(function ($row) {
                return $row->toArray();
            })->all();
        }

        return [
            'breadcrumb' => $this->breadcrumb
        ];
    }
}
