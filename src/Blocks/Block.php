<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\BlockInterface;
use Coretik\Core\Models\Traits\{
    Initializable,
    Bootable
};
use Coretik\PageBuilder\Blocks\Traits\{
    Grid,
    Faker,
};

use function Globalis\WP\Cubi\include_template_part;

#[\AllowDynamicProperties]
abstract class Block implements BlockInterface
{
    use Initializable;
    use Bootable;
    use Grid;
    use Faker;

    // Identifier
    const NAME = '';

    // Human label
    const LABEL = '';

    // Human category name
    const CATEGORY = '';

    // Define the block as user pickable
    const IN_LIBRARY = true;

    // Define the block as usuable in container block type
    const CONTAINERIZABLE = true;

    // Define the block to be screeshotable with the wp-cli task
    const SCREENSHOTABLE = true;
    const SCREEN_PREVIEW_SIZE = [1200, 542]; // coeff 2.21

    // Custom php template path to render block
    const TEMPLATE_PATH = null;

    // Custom thumbnail path to preview block in library
    const THUMBNAIL_PATH = null;

    protected string $uniqId;
    protected $context = null;
    protected $fields;
    private $wrappers = [];
    protected $settings = [];
    protected $modifiers = [];
    protected $wrapperParameters = [];
    protected static $fieldsHooked = [];
    protected $propsFilled = [];
    protected static $configGlobal = [];
    protected $config = [];

    abstract public function toArray();

    public function __construct(array $props = [], array $config = [])
    {
        static::bootIfNotBooted();
        $this->initialize();
        $this->uniqId = uniqid($this->getName() . '-');

        \do_action('pagebuilder/block/initialize', $props, $config, $this);
        \do_action('pagebuilder/block/initialize/name=' . $this->getName(), $props, $config, $this);

        $props = \apply_filters('pagebuilder/block/props', $props, $this);
        $props = \apply_filters('pagebuilder/block/props/name=' . $this->getName(), $props, $this);

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

    public function config(string $key): mixed
    {
        return $this->config[$key] ?? static::$configGlobal[$key] ?? null;
    }

    public function getUniqId(): string
    {
        return $this->uniqId;
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

    public function setContext($context): self
    {
        $this->context = $context;
        return $this;
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
        return $this->applyModifiers($this->fields);
    }

    public function resetFields(): self
    {
        $this->fields = null;
        return $this;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $block = $this;
        return include \locate_template($this->templateFields() . \str_replace('.', DIRECTORY_SEPARATOR, static::NAME) . '.php');
    }

    public function flexibleLayoutArgs(): array
    {
        return [];
    }

    public function fieldsBuilderConfig(array $config = []): array
    {
        $config = \wp_parse_args($config, [
            'label' => __(static::LABEL, app()->get('settings')['text-domain']),
            'display' => 'row',
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

        $config = \apply_filters('coretik/page-builder/block/fields-builder-config', $config, $this->getName(), $this);
        $config = \apply_filters('coretik/page-builder/block/fields-builder-config/name=' . $this->getName(), $config, $this);
        return $config;
    }

    public function adminTemplate($withExt = true): string
    {
        return \sprintf('%s%s/render%s', $this->config('blocks.acf.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
    }

    public function template($withExt = true): string
    {
        return static::TEMPLATE_PATH ?? \sprintf('%s%s%s', $this->config('blocks.template.directory'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME), $withExt ? '.php' : '');
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
        return static::THUMBNAIL_PATH ?? \sprintf('%s%s.png', $this->config('fields.thumbnails.baseUrl'), \str_replace('.', DIRECTORY_SEPARATOR, static::NAME));
    }

    public static function category(): string
    {
        if (!empty(static::CATEGORY)) {
            return static::CATEGORY;
        }

        $name = explode('.', static::NAME, 2);

        $category = $name[0];
        $category = \apply_filters('coretik/page-builder/block/category', $category, static::NAME);
        $category = \apply_filters('coretik/page-builder/block/category/name=' . static::NAME, $category);

        switch ($category) {
            case 'headings':
                return __('Titres', app()->get('settings')['text-domain']);
            case 'content':
                return __('Contenus', app()->get('settings')['text-domain']);
            case 'editorial':
                return __('Éditorial', app()->get('settings')['text-domain']);
            case 'multimedia':
                return __('Multimédia', app()->get('settings')['text-domain']);
            case 'tools':
                return __('Outils', app()->get('settings')['text-domain']);
            case 'containers':
                return __('Conteneurs', app()->get('settings')['text-domain']);
            case 'layouts':
                return __('Dispositions prédéfinies', app()->get('settings')['text-domain']);
            case 'templates':
                return __('Modèles de page', app()->get('settings')['text-domain']);
            case 'posts':
                return __('Blog', app()->get('settings')['text-domain']);
            default:
                return __($category, app()->get('settings')['text-domain']);
        }
    }

    public function templateFields(): string
    {
        return $this->config('fields.directory') ?? '';
    }

    public function addSettings(callable $provider, int $priority = 10): self
    {
        $this->settings[$priority][] = $provider;
        return $this;
    }

    public function fieldSettingsName(): string
    {
        return $this->getName() . '_settings';
    }

    public function fieldSettingsTitle(): string
    {
        return __('Paramètres du bloc ' . lcfirst($this->getLabel()), app()->get('settings')['text-domain']);
    }

    /**
     * Add settings fields on existings fieldgroup;
     */
    protected function applySettings(FieldsBuilder $field): FieldsBuilder
    {
        \ksort($this->settings, SORT_NUMERIC);
        foreach ($this->settings as $priority => $callables) {
            foreach ($callables as $callable) {
                $field->addFields($callable());
            }
        }

        return $field;
    }

    /**
     * Provide an existings fieldgroup and create an accordion field if missing and append settings fields;
     */
    public function useSettingsOn(FieldsBuilder $field): self
    {
        if (empty($this->settings)) {
            return $this;
        }

        $accordion = $this->fieldSettingsName() . '_accordion';
        if (!$field->fieldExists($accordion)) {
            $field->addAccordion($this->fieldSettingsName(), ['label' => $this->fieldSettingsTitle()]);
        } else {
            $field->getField($accordion);
        }

        $this->applySettings($field);

        return $this;
    }

    public function addWrapper(callable $wrapper, int $priority = 10): self
    {
        $this->wrappers[$priority][] = $wrapper;
        return $this;
    }

    public function wrapperParameters(): array
    {
        $parameters = [];
        foreach ($this->wrapperParameters as $key) {
            $parameters[$key] = $this->$key ?? null;
        }
        return $parameters;
    }

    protected function applyWrappers(string $output): string
    {
        \ksort($this->wrappers, SORT_NUMERIC);
        foreach ($this->wrappers as $priority => $callables) {
            foreach ($callables as $callable) {
                $output = \call_user_func($callable, $output, $this);
            }
        }

        return $output;
    }

    public function addModifier(callable $modifier, int $priority = 10): self
    {
        $this->modifiers[$priority][] = $modifier;
        return $this;
    }

    protected function applyModifiers(FieldsBuilder $fields): FieldsBuilder
    {
        \ksort($this->modifiers, SORT_NUMERIC);
        foreach ($this->modifiers as $priority => $callables) {
            foreach ($callables as $callable) {
                $fields = \call_user_func($callable, $fields, $this);
            }
        }
        return $fields;
    }

    protected function getPlainHtml(array $parameters): string
    {
        return include_template_part($this->template(false), $parameters, true);
    }

    /**
     * Rendering block
     * Return or echo html
     */
    public function render($return = false): string
    {
        \do_action('coretik/page-builder/block/before_render', $this);
        \do_action('coretik/page-builder/block/before_render/name=' . $this->getName(), $this);

        $output = $this->getPlainHtml($this->toArray() + ['context' => $this->context()]);
        $output = $this->applyWrappers($output);

        if (!$return) {
            echo $output;
        }

        return $output;
    }

    public function __toString()
    {
        return $this->render();
    }
}
