<?php

namespace Coretik\PageBuilder\Core\Block;

use Coretik\Core\Models\Traits\{
    Initializable,
    Bootable
};
use Coretik\Core\Utils\Classes;
use Coretik\PageBuilder\Core\Block\BlockContextType;
use Coretik\PageBuilder\Core\Block\Traits\{
    Faker,
    Settings,
    Wrappers,
    Modifiers,
    DevTools,
};
use Coretik\PageBuilder\Core\Contract\BlockContextInterface;
use Coretik\PageBuilder\Core\Contract\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;

use function Globalis\WP\Cubi\include_template_part;

#[\AllowDynamicProperties]
abstract class Block implements BlockInterface
{
    use Initializable;
    use Bootable;
    use Faker;
    use Settings;
    use Wrappers;
    use Modifiers;
    use DevTools;

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
    const FLEXIBLE_LAYOUT_ARGS = [];

    protected static $configGlobal = [];
    protected $config = [];
    protected string $uniqId;
    protected string $layoutId;
    protected $fields;
    protected $propsFilled = [];
    protected static int $counter = 0;
    protected ?BlockContextInterface $context = null;

    abstract public function toArray();

    public function __construct(array $props = [], array $config = [])
    {
        static::bootIfNotBooted();
        $this->initialize();

        \do_action('coretik/page-builder/block/initialize', $props, $config, $this);
        \do_action('coretik/page-builder/block/initialize/name=' . $this->getName(), $props, $config, $this);

        $props = \apply_filters('coretik/page-builder/block/props', $props, $this);
        $props = \apply_filters('coretik/page-builder/block/props/name=' . $this->getName(), $props, $this);

        if (empty($props['layoutId']) && empty($this->layoutId)) {
            $props['layoutId'] = sprintf('%s-%s', $this->getName(), static::$counter++);
        }

        $this->setProps($props);
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
        if (empty($this->uniqId)) {
            $this->uniqId = sprintf('%s-%s', $this->getName(), uniqid());
        }
        return $this->uniqId;
    }

    public function getLayoutId(): string
    {
        return $this->layoutId;
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getLabel(): string
    {
        return static::LABEL;
    }

    public function getCategory(): string
    {
        return static::category();
    }

    public function getCategoryTitle(): string
    {
        return static::categoryTitle();
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

    public function setContext(BlockContextInterface $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getContext(): ?BlockContextInterface
    {
        return $this->context;
    }

    public function fields()
    {
        if (empty($this->fields)) {
            \do_action('coretik/page-builder/block/before_generate_fields', $this);
            \do_action('coretik/page-builder/block/before_generate_fields/name=' . $this->getName(), $this);

            $fields = \apply_filters('coretik/page-builder/block/generate_fields', $this->fieldsBuilder(), $this);
            $fields = \apply_filters('coretik/page-builder/block/generate_fields/name=' . $this->getName(), $fields, $this);
            $this->fields = $fields;

            \do_action('coretik/page-builder/block/after_generate_fields', $this);
            \do_action('coretik/page-builder/block/after_generate_fields/name=' . $this->getName(), $this);
        }

        return $this->applyModifiers($this->fields);
    }

    public function resetFields(): self
    {
        $this->fields = null;
        return $this;
    }

    public function fieldsBuilderTemplate(): string
    {
        return $this->config('fields.directory') ?? '';
    }

    public function createFieldsBuilder(): FieldsBuilder
    {
        return new FieldsBuilder($this->getName(), $this->fieldsBuilderConfig());
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $block = $this;
        $field = $this->createFieldsBuilder();
        return include \locate_template($this->fieldsBuilderTemplate() . \str_replace('.', DIRECTORY_SEPARATOR, static::NAME) . '.php');
    }

    final public function flexibleLayoutArgs(): array
    {
        $args = [
            'layoutId' => $this->getLayoutId(),
        ];

        $custom_args = static::FLEXIBLE_LAYOUT_ARGS;
        if (!\is_array($custom_args)) {
            $custom_args = [];
        }

        return \apply_filters('coretik/page-builder/block/flexible_layout_args', $args + $custom_args, $this);
    }

    public function fieldsBuilderConfig(array $config = []): array
    {
        $config = \wp_parse_args($config, [
            'label' => __(static::LABEL, app()->get('settings')['text-domain']),
            'display' => 'row',
            'acfe_flexible_thumbnail' => $this->thumbnail(),
            'acfe_flexible_category' => $this->getCategoryTitle(),
            'acfe_flexible_render_template' => $this->adminTemplate(),
            'acfe_flexible_render_style' => $this->adminStyle(),
            'acfe_flexible_render_script' => $this->adminScript(),
            'acfe_flexible_settings' => '',
            'acfe_flexible_settings_size' => 'medium',
        ]);

        $config = \apply_filters('coretik/page-builder/block/fields-builder-config', $config, $this->getName(), $this);
        $config = \apply_filters('coretik/page-builder/block/fields-builder-config/name=' . $this->getName(), $config, $this);
        return $config;
    }

    protected function adminResourcePath(): string
    {
        return $this->config('blocks.acf.directory') . \str_replace('.', DIRECTORY_SEPARATOR, $this->getName());
    }

    public function adminTemplate($withExt = true): string
    {
        return \sprintf('%s/render%s', $this->adminResourcePath(), $withExt ? '.php' : '');
    }

    public function adminStyle(): string
    {
        return \sprintf('%s/style.css', $this->adminResourcePath());
    }

    public function adminScript(): string
    {
        return \sprintf('%s/script.js', $this->adminResourcePath());
    }

    public function thumbnail(): string
    {
        return static::THUMBNAIL_PATH ?? \sprintf('%s%s.png', $this->config('fields.thumbnails.baseUrl'), \str_replace('.', DIRECTORY_SEPARATOR, $this->getName()));
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

        return $category;
    }

    public static function categoryTitle(): string
    {
        $category = static::category();

        $categoryTitle = match ($category) {
            'headings' => __('Titres', app()->get('settings')['text-domain']),
            'content' => __('Contenus', app()->get('settings')['text-domain']),
            'editorial' => __('Éditorial', app()->get('settings')['text-domain']),
            'multimedia' => __('Multimédia', app()->get('settings')['text-domain']),
            'tools' => __('Outils', app()->get('settings')['text-domain']),
            'containers' => __('Conteneurs', app()->get('settings')['text-domain']),
            'layouts' => __('Dispositions prédéfinies', app()->get('settings')['text-domain']),
            'templates' => __('Modèles de page', app()->get('settings')['text-domain']),
            'posts' => __('Blog', app()->get('settings')['text-domain']),
            default => $category,
        };

        $categoryTitle = \apply_filters('coretik/page-builder/block/category-title', $categoryTitle, $category);
        $categoryTitle = \apply_filters('coretik/page-builder/block/category-title/category=' . $category, $categoryTitle);

        return $categoryTitle;
    }

    public function getParameters(): array
    {
        $parameters = ['uniqId' => $this->getUniqId(), 'layoutId' => $this->getLayoutId()] + $this->toArray() + ['context' => $this->getContext()?->toArray()];

        // Call toArray from traits
        foreach (Classes::classUsesDeep($this) as $traitNamespace) {
            $ref = new \ReflectionClass($traitNamespace);
            $traitName = $ref->getShortName();
            $method = $traitName . 'ToArray';
            $method = strtolower(substr($method, 0, 1)) . substr($method, 1);

            if (method_exists($this, $method)) {
                $parameters = array_merge($parameters, $this->$method());
            }
        }

        return $parameters;
    }

    public function template($withExt = true): string
    {
        return static::TEMPLATE_PATH ?? \sprintf('%s%s%s', $this->config('blocks.template.directory'), \str_replace('.', DIRECTORY_SEPARATOR, $this->getName()), $withExt ? '.php' : '');
    }

    public function templateExists(): bool
    {
        return !empty(locate_template($this->template()));
    }

    protected function getPlainHtml(array $parameters): string
    {
        if (!$this->templateExists()) {
            return \apply_filters(
                'coretik/page-builder/block/template_placehoder',
                sprintf('<p>[%s] No template found</p>', $this->getName()),
                $this,
                $parameters
            );
        }

        return include_template_part($this->template(false), $parameters, true);
    }

    /**
     * Rendering block
     * Return or echo html
     */
    public function render($return = false): string
    {
        \do_action('coretik/page-builder/block/before_render', $this, $return);
        \do_action('coretik/page-builder/block/before_render/name=' . $this->getName(), $this, $return);
        \do_action('coretik/page-builder/block/before_render/id=' . $this->getUniqId(), $this, $return);

        $output = \apply_filters('coretik/page-builder/block/start_output', '', $this, $return);
        $output = \apply_filters('coretik/page-builder/block/start_output/name=' . $this->getName(), $output, $this, $return);

        $output = $this->getPlainHtml($this->getParameters());
        $output = $this->applyWrappers($output);

        $output = \apply_filters('coretik/page-builder/block/end_output', $output, $this, $return);
        $output = \apply_filters('coretik/page-builder/block/end_output/name=' . $this->getName(), $output, $this, $return);

        if (!$return) {
            echo $output;
        }

        \do_action('coretik/page-builder/block/after_render', $this, $return);
        \do_action('coretik/page-builder/block/after_render/name=' . $this->getName(), $this, $return);
        \do_action('coretik/page-builder/block/after_render/id=' . $this->getUniqId(), $this, $return);

        return $output;
    }

    public function isChild(): bool
    {
        $context = $this->getContext();

        if (empty($context)) {
            return false;
        }

        return $context->getType() === BlockContextType::PARENT;
    }

    public function getParent(): ?BlockInterface
    {
        if (!$this->isChild()) {
            return null;
        }

        return $this->getContext()->getBlock();
    }

    public function isParent(): bool
    {
        return !$this->isChild();
    }

    public function __toString()
    {
        return $this->render();
    }
}
