<?php

namespace Coretik\PageBuilder\Core\Contract;

interface JobInterface
{
    public function handle(): void;
    public function getPayload(): array;
}
