<?php

namespace Themetik\Services\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Themetik\Services\PageBuilder\BlockInterface;
use Coretik\Core\Models\Traits\Initializable;
use Coretik\Core\Models\Traits\Bootable;
use Themetik\Services\PageBuilder\Blocks\Traits\Grid;

use function Globalis\WP\Cubi\include_template_part;

abstract class Block implements BlockInterface
{
    use Initializable;
    use Grid;

    const NAME = '';
    const LABEL = '';
    const SCREENSHOTABLE = true;
    const SCREEN_PREVIEW_SIZE = [1200, 542]; // coeff 2.21
    const CATEGORY = '';
    const DIRECTORY_FIELDS = 'src/admin/fields/blocks/';
    const DIRECTORY_THUMBS = 'images/admin/acf/';

    protected $context = null;
    protected $fields;
    private $wrappers = [];
    protected $wrapperParameters = [];
    protected static $fieldsHooked = [];
    protected $propsFake = [];
    protected $propsFilled = [];

    abstract function toArray();

    public function __construct(array $props = [])
    {
        $this->initialize();
        $this->setProps($props);

        if (\is_admin()) {
            if (!in_array($this->getName(), static::$fieldsHooked)) {
                $block = $this;
                \add_action('acfe/flexible/render/before_template/layout=' . $this->fields()->getName(), function () use ($block) {
                    $data = get_fields();
                    $data = current(current($data));
                    $block->setProps($data)->render();
                });
                static::$fieldsHooked[] = $this->getName();
            }
        }
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getLabel(): string
    {
        return static::LABEL;
    }

    public function setProps(array $props)
    {
        foreach ($props as $key => $value) {
            if (\property_exists($this, $key)) {
                $this->$key = $value;
                $this->propsFilled[$key] = $value;
            }
        }
        return $this;
    }

    public function getPropsFilled(): array
    {
        return $this->propsFilled;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function context()
    {
        return $this->context;
    }

    public function fields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->fieldsBuilder();
        }
        return $this->fields;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $block = $this;
        return include locate_template($this->templateFields() . \str_replace('.', DIRECTORY_SEPARATOR, static::NAME) . '.php');
    }

    public function fieldsBuilderConfig(array $config = []): array
    {
        return \wp_parse_args($config, [
            'label' => __(static::LABEL, 'themetik'),
            'display' => 'block',
            'acfe_flexible_thumbnail' => $this->thumbnail(),
            'acfe_flexible_category' => $this->category(),
            'acfe_flexible_render_template' => $this->adminTemplate(),
            'acfe_flexible_render_style' => $this->style(),
            'acfe_flexible_render_script' => $this->script(),
            'acfe_flexible_settings' => '',
            'acfe_flexible_settings_size' => 'medium',
            'acfe_layout_col' => '12',
            'acfe_layout_allowed_col' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        ]);
    }

    public function adminTemplate($withExt = true): string
    {
        return sprintf('templates/acf/%s/render%s', \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
    }

    public function template($withExt = true): string
    {
        return sprintf('templates/blocks/%s%s', \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
    }

    public function style(): string
    {
        return sprintf('templates/acf/%s/style.css', \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public function script(): string
    {
        return sprintf('templates/acf/%s/script.js', \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public function thumbnail(): string
    {
        return sprintf('<##ASSETS_URL##>/images/admin/acf/%s.png', \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public function category(): string
    {
        if (!empty(static::CATEGORY)) {
            return static::CATEGORY;
        }

        $name = explode('.', static::NAME, 2);
        switch ($name[0]) {
            case 'headings':
                return __('Titres', 'themetik');
            case 'content':
                return __('Contenus', 'themetik');
            case 'tools':
                return __('Outils', 'themetik');
            case 'containers':
                return __('Conteneurs', 'themetik');
            case 'layouts':
                return __('Dispositions prédéfinies', 'themetik');
            default:
                return __($name[0], 'themetik');
        }
    }

    public function templateFields(): string
    {
        return static::DIRECTORY_FIELDS;
    }

    public function addWrapper(callable $wrapper, int $priority = 10)
    {
        $this->wrappers[$priority][] = $wrapper;
    }

    public function wrapperParameters()
    {
        $parameters = [];
        foreach ($this->wrapperParameters as $key) {
            $parameters[$key] = $this->$key ?? null;
        }
        return $parameters;
    }

    public function render($return = false)
    {
        \do_action('themetik/services/page-builder/block/before_render', $this);
        \do_action('themetik/services/page-builder/block/before_render/name=' . $this->getName(), $this);

        $component = include_template_part($this->template(false), $this->toArray() + ['context' => $this->context()], true);
        \ksort($this->wrappers, SORT_NUMERIC);

        foreach ($this->wrappers as $priority => $callables) {
            foreach ($callables as $callable) {
                $component = \call_user_func($callable, $component, $this);
            }
        }

        if (!$return) {
            echo $component;
        }

        return $component;
    }

    public function fakeIt()
    {
        $props = \apply_filters('themetik/services/page-builder/fake-it/name=' . $this->getName(), $this->propsFake ?? []);

        $build = $this->fields()->build();
        foreach ($build['fields'] as $field) {
            if (!isset($props[$field['name']])) {
                $props[$field['name']] = static::fakeField($field);
            }
        }

        $this->setProps($props);
        return $this;
    }

    protected static function fakeField($field)
    {
        if (!empty($field['default_value'])) {
            return $field['default_value'];
        }

        switch ($field['type']) {
            case 'button_group':
            case 'radio':
            case 'select':
            case 'checkbox':
                $choices = array_keys($field['choices']);
                if (empty($choices)) {
                    return '';
                }
                $random = array_rand($choices);
                return $choices[$random];
            case 'repeater':
            case 'group':
                $fakeChildren = function () use ($field) {
                    $subfields = [];
                    foreach ($field['sub_fields'] as $subfield) {
                        $subfields[$subfield['name']] = static::fakeField($subfield);
                    }
                    return $subfields;
                };

                if ('repeater' === $field['type']) {
                    $subfields = [];
                    $min = $field['min'] ?? 0;
                    $max = $field['max'] ?? ($min + 3);
                    $average = ceil(($min + $max) / 2);
                    $counter = $average > 1 ? $average : ($min + 2);
                    for ($i = 0; $i < $counter; $i++) {
                        $subfields[] = $fakeChildren();
                    }
                    return $subfields;
                }

                return $fakeChildren();
            case 'relationship':
                $min = $field['min'] ?? 0;
                $max = $field['max'] ?? ($min + 3);
                $average = ceil(($min + $max) / 2);

                switch ($field['return_format']) {
                    case 'ids':
                        return \array_map(function () {
                            return app()->faker()->postId();
                        }, range(0, $average));
                    default:
                        return \array_map(function () {
                            return app()->faker()->post();
                        }, range(0, $average));
                }
            case 'gallery':
                return [
                    app()->faker()->attachmentId(),
                    app()->faker()->attachmentId(),
                    app()->faker()->attachmentId(),
                ];
            case 'image':
                return app()->faker()->attachmentId();
            case 'text':
                return app()->faker()->text(20);
            case 'textarea':
                return app()->faker()->text(50);
            case 'wysiwyg':
                return implode('', \array_map(function ($p) {
                    return sprintf('<p>%s</p>', $p);
                }, app()->faker()->paragraphs(5)));
            case 'page_link':
                return app()->faker()->pageId();
            case 'true_false':
                return rand(0, 1);
            case 'number':
                return rand($field['min'] ?? 0, $field['max'] ?? 100);
            default:
                return '';
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}
