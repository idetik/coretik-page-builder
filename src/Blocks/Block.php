<?php

namespace Coretik\PageBuilder\Blocks;

use StoutLogic\AcfBuilder\FieldsBuilder;
use Coretik\PageBuilder\BlockInterface;
use Coretik\Core\Models\Traits\{
    Initializable,
    Bootable
};
use Coretik\Core\Utils\Classes;
use Coretik\PageBuilder\Blocks\Traits\{
    Grid,
    Faker,
    Settings,
    Wrappers,
    Modifiers,
};
use Coretik\PageBuilder\Blocks\Modifier\PersistantIdModifier;

use function Globalis\WP\Cubi\include_template_part;

#[\AllowDynamicProperties]
abstract class Block implements BlockInterface
{
    use Initializable;
    use Bootable;
    // use Grid;
    use Faker;
    use Settings;
    use Wrappers;
    use Modifiers;

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
    protected static $fieldsHooked = [];
    protected $propsFilled = [];
    protected static $configGlobal = [];
    protected $config = [];
    protected static int $counter = 0;

    abstract public function toArray();

    public function __construct(array $props = [], array $config = [])
    {
        static::$counter++;
        static::bootIfNotBooted();
        $this->initialize();

        \do_action('pagebuilder/block/initialize', $props, $config, $this);
        \do_action('pagebuilder/block/initialize/name=' . $this->getName(), $props, $config, $this);

        $props = \apply_filters('pagebuilder/block/props', $props, $this);
        $props = \apply_filters('pagebuilder/block/props/name=' . $this->getName(), $props, $this);

        if (empty($props['uniqId']) && empty($this->uniqId)) {
            $props['uniqId'] = sprintf('%s-%s', $this->getName(), static::$counter);
        }

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

    public function setContext($context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getContext()
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

    public function fieldsBuilderTemplate(): string
    {
        return $this->config('fields.directory') ?? '';
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $block = $this;
        return include \locate_template($this->fieldsBuilderTemplate() . \str_replace('.', DIRECTORY_SEPARATOR, static::NAME) . '.php');
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
            'acfe_flexible_category' => $this->getCategoryTitle(),
            'acfe_flexible_render_template' => $this->adminTemplate(),
            'acfe_flexible_render_style' => $this->adminStyle(),
            'acfe_flexible_render_script' => $this->adminScript(),
            'acfe_flexible_settings' => '',
            'acfe_flexible_settings_size' => 'medium',
            'acfe_layout_col' => '12',
            'acfe_layout_allowed_col' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
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

    protected function getParameters(): array
    {
        $parameters = $this->toArray() + ['context' => $this->getContext()];

        // Call toArray from traits
        foreach (Classes::classUsesDeep($this) as $traitNamespace) {
            $ref = new \ReflectionClass($traitNamespace);
            $traitName = $ref->getShortName();
            $method = $traitName . 'ToArray';
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

        $output = $this->getPlainHtml($this->getParameters());
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
