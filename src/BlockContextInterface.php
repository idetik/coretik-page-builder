<?php

namespace Coretik\PageBuilder;

interface BlockContextInterface
{
    public function getBlock(): BlockInterface;
    public function getName(): string;
    public function getCategory(): string;
    public function getData();
    public function getType(): BlockContextType;
    public function toArray(): array;
}
