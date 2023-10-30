<?php

namespace Coretik\PageBuilder\Job;

interface JobInterface
{
    public function handle(): void;
    public function getPayload(): array;
}