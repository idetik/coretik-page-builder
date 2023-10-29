<?php

namespace Coretik\PageBuilder\Blocks\Modifier;

use Coretik\PageBuilder\BlockInterface;
use StoutLogic\AcfBuilder\FieldsBuilder;
use Exception;


class ColumnModifier extends Modifier
{
    const NAME = 'column';
    const PRIORITY = 100;

    protected $uniqId;

    public static function make(mixed $args = null): self
    {
        if (empty($args) || !\is_int($args)) {
            throw new Exception('Integer ColumnModifier args is expected.');
        }
        
        return parent::make($args);
    }

    /**
     * Wrap fields in column
     */
    public function handle(FieldsBuilder $fields, BlockInterface $block): FieldsBuilder
    {
        $wrapper = new FieldsBuilder($this->getUniqId() . '-wrapper');
        $wrapper
            ->addField($this->getUniqId() . 'column', 'acfe_column', [
                'columns' => $this->getArgs() . '/12'
            ])
            ->addFields($fields);

        return $fields;
    }

    protected function setUniqId(): self
    {
        $this->uniqId = uniqid($this->getName());
        return $this;
    }

    protected function getUniqId(): string
    {
        if (empty($this->uniqId)) {
            $this->setUniqId();
        }
        return $this->uniqId;
    }
}
