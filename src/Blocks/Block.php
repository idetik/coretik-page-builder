<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\BlockInterface;
use Coretik\Core\Models\Traits\{Initializable, Bootable};
use Coretik\PageBuilder\Blocks\Traits\Grid;

use function Globalis\WP\Cubi\include_template_part;

abstract class Block implements BlockInterface
{
    use Initializable;
    use Bootable;
    use Grid;

    const NAME = '';
    const LABEL = '';
    const IN_LIBRARY = true;
    const CONTAINERIZABLE = true;
    const SCREENSHOTABLE = true;
    const SCREEN_PREVIEW_SIZE = [1200, 542]; // coeff 2.21
    const CATEGORY = '';

    protected $context = null;
    protected $fields;
    private $wrappers = [];
    protected $settings = [];
    protected $wrapperParameters = [];
    protected static $fieldsHooked = [];
    protected $propsFake = [];
    protected $propsFilled = [];

    protected static $configGlobal = [];
    protected $config = [];

    abstract public function toArray();

    public function __construct(array $props = [], array $config = [])
    {
        static::bootIfNotBooted();
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

    public static function setConfigAsGlobal($config): void
    {
        static::$configGlobal = $config;
    }

    protected function config(string $key): mixed
    {
        return $this->config[$key] ?? static::$configGlobal[$key] ?? null;
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

    public function flexibleLayoutArgs(): array
    {
        return [];
    }

    public function fieldsBuilderConfig(array $config = []): array
    {
        return \wp_parse_args($config, [
            'label' => __(static::LABEL, app()->get('settings')['text-domain']),
            'display' => 'block',
            'acfe_flexible_thumbnail' => $this->thumbnail(),
            'acfe_flexible_category' => static::category(),
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
        return \sprintf('%s%s/render%s', $this->config('blocks.acf.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
    }

    public function template($withExt = true): string
    {
        return \sprintf('%s%s%s', $this->config('blocks.template.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
    }

    public function style(): string
    {
        return \sprintf('%s%s/style.css', $this->config('blocks.acf.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public function script(): string
    {
        return \sprintf('%s%s/script.js', $this->config('blocks.acf.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public function thumbnail(): string
    {
        return \sprintf('%s%s.png', $this->config('fields.thumbnails.baseUrl'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public static function category(): string
    {
        if (!empty(static::CATEGORY)) {
            return static::CATEGORY;
        }

        $name = explode('.', static::NAME, 2);
        switch ($name[0]) {
            case 'headings':
                return __('Titres', app()->get('settings')['text-domain']);
            case 'content':
                return __('Contenus', app()->get('settings')['text-domain']);
            case 'tools':
                return __('Outils', app()->get('settings')['text-domain']);
            case 'containers':
                return __('Conteneurs', app()->get('settings')['text-domain']);
            case 'layouts':
                return __('Dispositions prédéfinies', app()->get('settings')['text-domain']);
            default:
                return __($name[0], app()->get('settings')['text-domain']);
        }
    }

    public function templateFields(): string
    {
        return $this->config('fields.directory') ?? '';
    }

    public function addSettings(callable $provider, int $priority = 10)
    {
        $this->settings[$priority][] = $provider;
    }

    public function fieldSettingsName()
    {
        return $this->getName() . '_settings';
    }

    public function fieldSettingsTitle()
    {
        return __('Paramètres du bloc ' . lcfirst($this->getLabel()), app()->get('settings')['text-domain']);
    }

    public function useSettingsOn(FieldsBuilder $field)
    {
        if (empty($this->settings)) {
            return;
        }

        $accordion = $this->fieldSettingsName() . '_accordion';
        if (!$field->fieldExists($accordion)) {
            $field->addAccordion($this->fieldSettingsName(), ['label' => $this->fieldSettingsTitle()]);
        } else {
            $field->getField($accordion);
        }

        \ksort($this->settings, SORT_NUMERIC);

        foreach ($this->settings as $priority => $callables) {
            foreach ($callables as $callable) {
                $field->addFields($callable());
            }
        }
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
        \do_action('coretik/page-builder/block/before_render', $this);
        \do_action('coretik/page-builder/block/before_render/name=' . $this->getName(), $this);

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
        $props = \apply_filters('coretik/page-builder/fake-it/name=' . $this->getName(), $this->propsFake ?? []);

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

        $fakeField = \apply_filters('coretik/page-builder/block/fake-field', '', $field['type'], $field);
        if (!empty($fakeField)) {
            return $fakeField;
        }

        switch ($field['type']) {
            case 'button_group':
            case 'radio':
            case 'select':
                $choices = array_keys($field['choices']);
                if (empty($choices)) {
                    return '';
                }
                $random = array_rand($choices);
                return $choices[$random];
            case 'checkbox':
                $choices = array_keys($field['choices']);
                if (empty($choices)) {
                    return [];
                }
                $random = array_rand($choices);
                return [$choices[$random]];
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
            case 'date_time_picker':
                return app()->faker()->date('Y-m-d H:i:s');
            default:
                return '';
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}
