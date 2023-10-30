<?php

namespace Coretik\PageBuilder\Job\Thumbnail;

use Coretik\PageBuilder\Job\JobInterface;

class GenerateThumbnailJob implements JobInterface
{
    protected $generator;
    protected $layout;
    protected $override;
    protected $verbose;
    protected $payload;

    public function __construct($generator, array $config = [])
    {
        $this->generator = $generator;

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

    public function handle(): void
    {
        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== GenerateThumnail - Job start ========' . PHP_EOL);
        }

        if (!empty($this->layout)) {
            try {
                [$block, $output] = @$this->generator->generateThumb($this->layout, $this->override);
                $results = [$block->getName() => $output];
                if ($this->verbose) {
                    app()->notices()->success(sprintf('%s : %s', $block->getLabel(), $output));
                }
            } catch (\Exception $e) {
                $results['errors'][$this->layout] = $e->getMessage();
                if ($this->verbose) {
                    app()->notices()->error(sprintf('%s : %s', $this->layout, $e->getMessage()));
                }
            }
        } else {
            $results = @$this->generator->generateThumbs($this->override, $this->verbose);
        }

        if ($this->verbose) {
            app()->notices()->info(PHP_EOL . '======== GenerateThumnail - Job end ========' . PHP_EOL);
        }

        $this->setPayload($results);
    }

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
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
