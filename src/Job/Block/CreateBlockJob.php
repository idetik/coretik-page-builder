<?php

namespace Coretik\PageBuilder\Job\Block;

use Coretik\PageBuilder\Job\JobInterface;

class CreateBlockJob implements JobInterface
{
    protected BlockType $blockType;
    protected $layout;
    protected $override;
    protected $verbose;
    protected array $payload = [];

    public function __construct(array $config = [])
    {
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

    public function setBlockType(BlockType $blockType): self
    {
        $this->blockType = $blockType;
        return $this;
    }

    protected static function getStubFile(string $name): string
    {
        return __DIR__ . '/stubs/' . $name . '.stub';
    }

    public function handle(): void
    {
        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job start ========' . PHP_EOL);
        }


        try {

            // Create ClassFile
            $stubFile = match ($this->blockType) {
                BlockType::Component => static::getStubFile('block-component'),
                BlockType::Composite => static::getStubFile('block-composite'),
                BlockType::Block => static::getStubFile('block'),
            };

            // $dir = $this->

            var_dump($stubFile);
            // app()->notices()->info($stubFile);
            die;


            if ($this->verbose) {
                app()->notices()->success(sprintf('%s : %s', $block->getLabel(), $output));
            }
        } catch (\Exception $e) {
            $results['errors'][$this->layout] = $e->getMessage();
            if ($this->verbose) {
                app()->notices()->error(sprintf('%s : %s', $this->layout, $e->getMessage()));
            }
        }

        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== CreateBlock - Job end ========' . PHP_EOL);
        }
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
