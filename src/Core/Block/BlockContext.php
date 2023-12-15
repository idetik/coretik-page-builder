<?php

namespace Coretik\PageBuilder\Core\Block;

use Coretik\PageBuilder\Core\Contract\BlockContextInterface;
use Coretik\PageBuilder\Core\Contract\BlockInterface;
use Coretik\PageBuilder\Core\Block\BlockContextType;

class BlockContext implements BlockContextInterface
{
    protected BlockInterface $block;
    protected string $name;
    protected string $category;
    protected mixed $data = null;
    protected BlockContextType $type = BlockContextType::OTHER;

    public function __construct(
        ?BlockInterface $block = null,
        ?string $name = null,
        ?string $category = null,
        mixed $data = null,
    ) {
        if (!empty($block)) {
            $this->setBlock($block);
        }

        if (!empty($name)) {
            $this->setName($name);
        }

        if (!empty($category)) {
            $this->setCategory($category);
        }

        if (!empty($data)) {
            $this->setData($data);
        }
    }

    public static function contextualize(BlockInterface $block): self
    {
        $context = new static();
        $context->setBlock($block);
        $context->setName($block->getName());
        $context->setCategory($block->getCategory());
        return $context;
    }

    public function getBlock(): BlockInterface
    {
        return $this->block;
    }

    public function setBlock(BlockInterface $block): self
    {
        $this->block = $block;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getType(): BlockContextType
    {
        return $this->type;
    }

    public function setType(BlockContextType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'uniqId' => $this->getBlock()->getUniqId(),
            'category' => $this->getCategory(),
            'data' => $this->getData(),
            'type' => $this->getType()->name,
        ];
    }
}
