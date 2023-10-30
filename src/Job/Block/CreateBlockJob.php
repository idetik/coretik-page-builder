<?php

namespace Coretik\PageBuilder\Job\Block;

class CreateBlockJob
{
    protected BlockType $blockType;
    protected $layout;
    protected $override;
    protected $verbose;

    public function __construct(BlockType $blockType, array $config = [])
    {
        $this->blockType = $blockType;

        if (!empty($config)) {
            $this->setConfig($config);
        }
    }

    public function setConfig(array $config): self
    {
        $this->layout = $config['layout'] ?? null;
        $this->override = $config['override'] ?? false;
        $this->verbose = $config['verbose'] ?? false;
        return $this;
    }

    protected static function getStubFile(string $name): string
    {
        return __DIR__ . '/stubs/' . $name . '.stub';
    }

    public function handle()
    {
        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job start ========' . PHP_EOL);
        }

        if (!empty($this->layout)) {
            try {

                // Create ClassFile
                $stubFile = match ($this->blockType) {
                    BlockType::Component => static::getStubFile('block-component'),
                    BlockType::Composite => static::getStubFile('block-composite'),
                    BlockType::Block => static::getStubFile('block'),
                };


                if ($this->verbose) {
                    app()->notices()->success(sprintf('%s : %s', $block->getLabel(), $output));
                }
            } catch (\Exception $e) {
                $results['errors'][$this->layout] = $e->getMessage();
                if ($this->verbose) {
                    app()->notices()->error(sprintf('%s : %s', $this->layout, $e->getMessage()));
                }
            }
        }

        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job end ========' . PHP_EOL);
        }

        return $results;
    }

    public function __invoke()
    {
        $this->handle();
    }
}
