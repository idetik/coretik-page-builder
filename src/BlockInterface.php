<?php

namespace Coretik\PageBuilder;

interface BlockInterface
{
    public function render();
    public function toArray();
}
