<?php

namespace Coretik\PageBuilder\Core\Contract;

use StoutLogic\AcfBuilder\FieldsBuilder;

interface BlockInterface
{
    public static function supportsBlockType(): bool;
    public function getUniqId(): string;
    public function getLayoutId(): string;
    public function getName(): string;
    public function getLabel(): string;
    public function getCategory(): string;
    public function setProps(array $props);
    public function setContext(BlockContextInterface $context): self;
    public function getContext(): ?BlockContextInterface;
    public function fields(): FieldsBuilder;
    public function fieldsBuilder(): FieldsBuilder;
    public function render(bool $return = false);
    public function toArray();
}
