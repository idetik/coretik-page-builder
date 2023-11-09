<?php

namespace Coretik\PageBuilder\Core\Job\Block;

use Coretik\Core\Container;
use Coretik\PageBuilder\Core\Contract\JobInterface;
use Coretik\PageBuilder\Core\Job\Block\BlockType;
use Illuminate\Filesystem\Filesystem;

class CreateBlockJob implements JobInterface
{
    protected Container $app;
    protected BlockType $blockType;
    protected string $class;
    protected ?string $path;
    protected ?string $name;
    protected ?string $label;
    protected bool $force;
    protected bool $verbose;
    protected array $payload = [];
    protected Filesystem $files;

    public function __construct($app)
    {
        $this->app = $app;
        $this->files = new Filesystem;
    }

    public function setConfig(array $config): self
    {
        $this->class = $config['class'];
        $this->name = $config['name'] ?? null;
        $this->path = $config['path'] ?? null;
        $this->force = $config['force'] ?? false;
        $this->verbose = $config['verbose'] ?? true;
        $this->label = $config['label'] ?? null;
        return $this;
    }

    public function setBlockType(BlockType $blockType): self
    {
        $this->blockType = $blockType;
        return $this;
    }

    protected static function getStubFile(string $name): string
    {
        return __DIR__ . '/stubs/' . $name . '.stub';
    }

    protected function getNameValue(): string
    {
        return $this->name ?? strtolower(str_replace('/', '.', $this->class));
    }

    public function handle(): void
    {
        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job start ========' . PHP_EOL);
        }

        try {

            /**
             * Create ClassFile
             */
            $blockClassName = $this->qualifyClass($this->class);
            $blockClassPath = $this->getPath($blockClassName);

            // Next, We will check to see if the class already exists. If it does, we don't want
            // to create the class and overwrite the user's code. So, we will bail out so the
            // code is untouched. Otherwise, we will continue generating this class' files.
            if (!$this->force &&
                $this->alreadyExists($this->class)) {
                if ($this->verbose) {
                    app()->notices()->error($this->blockType->name . ' already exists.');
                }
                return;
            }

            // Next, we will generate the path to the location where this class' file should get
            // written. Then, we will build the class and make the proper replacements on the
            // stub files so that it gets the correctly formatted namespace and class name.
            $this->makeDirectory($blockClassPath);
            $this->files->put($blockClassPath, $this->buildClass($blockClassName));

            $info = $this->blockType;
            if ($this->verbose) {
                app()->notices()->success(sprintf('%s class          [%s] created successfully.', $info->name, $blockClassPath));
            }

            /**
             * Create template file
             */
            require_once $blockClassPath;

            $block = new $blockClassName;
            $templatePath = get_template_directory() . DIRECTORY_SEPARATOR . $block->template();

            if ($this->files->exists($templatePath)) {
                if ($this->verbose) {
                    app()->notices()->warning(sprintf('%s template       [%s] already exists.', $info->name, $blockClassPath));
                }
            } else {
                $this->makeDirectory($templatePath);
                $this->files->put($templatePath, '');
    
                if ($this->verbose) {
                    app()->notices()->success(sprintf('%s template       [%s] created successfully.', $info->name, $templatePath));
                }
            }

            /**
             * Create ACF files
             */
            foreach ([
                'admin-template' => 'adminTemplate',
                'admin-style' => 'adminStyle',
                'admin-script' => 'adminScript',
            ] as $key => $method) {
                $path = get_template_directory() . DIRECTORY_SEPARATOR . $block->$method();

                if ($this->files->exists($path)) {
                    if ($this->verbose) {
                        app()->notices()->warning(sprintf('%s %s [%s] already exists.', $info->name, str_pad($key, 14), $path));
                    }
                } else {
                    $this->makeDirectory($path);
                    $this->files->put($path, '');

                    if ($this->verbose) {
                        app()->notices()->success(sprintf('%s %s [%s] created successfully.', $info->name, str_pad($key, 14), $path));
                    }
                }
            }

            /**
             * Extends DI
             */
            app()->notices()->info('');
            app()->notices()->info('-------------------');
            app()->notices()->info('');
            app()->notices()->info(\WP_CLI::colorize('%U') . 'Don\'t forget to extends your block library:' . \WP_CLI::colorize('%n'));
            app()->notices()->info(\WP_CLI::colorize('%Y') . '$container->extend(\'pageBuilder.library\', fn ($blocks, $c) => $blocks->append(' . $blockClassName . '::class));' . \WP_CLI::colorize('%n'));

        } catch (\Exception $e) {
            if ($this->verbose) {
                app()->notices()->error(sprintf('%s : %s', $this->class, $e->getMessage()));
            }
        }

        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job end ========' . PHP_EOL);
        }
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (\str_starts_with($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            trim($rootNamespace, '\\').'\\'.$name
        );
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stubFile = match ($this->blockType) {
            BlockType::Component => static::getStubFile('block-component'),
            BlockType::Composite => static::getStubFile('block-composite'),
            BlockType::Block => static::getStubFile('block'),
        };

        $stub = $this->files->get($stubFile);

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name)->replaceValues($stub, $name);
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->app->get('pageBuilder.config')->get('blocks.rootNamespace');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        if (!empty($this->path)) {
            return $this->path;
        }
    
        $name = \str_replace($this->rootNamespace(), '', $name);
        $base = rtrim(\get_template_directory() . DIRECTORY_SEPARATOR . $this->app->get('pageBuilder.config')->get('blocks.src.directory'), '/');

        return $base . str_replace('\\', '/', $name).'.php';
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $searches = [
            ['{{ namespace }}', '{{ rootNamespace }}'],
            ['{{namespace}}', '{{rootNamespace}}'],
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace()],
                $stub
            );
        }

        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceClass(&$stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        $stub = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);

        return $this;
    }

    /**
     * Replace the name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceValues($stub, $name)
    {
        $values = [
            'name' => $this->getNameValue(),
            'label' => $this->label ?? current(array_reverse(explode('\\', $name))),
        ];

        $stub = str_replace(['DummyName', '{{ name }}', '{{name}}'], $values['name'], $stub);
        $stub = str_replace(['DummyLabel', '{{ label }}', '{{label}}'], $values['label'], $stub);

        return $stub;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }

        return $path;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function __invoke()
    {
        $this->handle();
    }
}
