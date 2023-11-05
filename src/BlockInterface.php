<?php

namespace Coretik\PageBuilder;

interface BlockInterface
{
    public function getUniqId(): string;
    public function getName(): string;
    public function getLabel(): string;
    public function render();
    public function toArray();
}
