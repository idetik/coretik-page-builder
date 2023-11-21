<?php

namespace Coretik\PageBuilder\Library\Component;

use Coretik\PageBuilder\Core\Block\BlockComponent;
use StoutLogic\AcfBuilder\FieldsBuilder;

class TableComponent extends BlockComponent
{
    const NAME = 'components.table';
    const LABEL = 'Tableau';

    const COLUMNS_MIN = 2;
    const COLUMNS_MAX = 6;

    protected $cols_number;
    protected $add_headline;
    protected $head;
    protected $body;

    protected function columnFactory($numbers): array
    {
        $columns = [];
        for ($i = 1; $i < $numbers + 1; $i++) {
            $column = new FieldsBuilder('columns_' . $i);
            $column_element = $column->addTextarea('column_' . $i, ['rows' => 3])->setRequired();

            if ($i > static::COLUMNS_MIN) {
                $column_element->conditional('cols_number', '>', $i - 1);
            }

            $columns[] = $column;
        }

        return $columns;
    }

    public function fieldsBuilder(): FieldsBuilder
    {
        $defaultColumns = 4;

        $columns = $this->columnFactory(static::COLUMNS_MAX);

        $field = $this->createFieldsBuilder();
        $field
            ->addNumber('cols_number', ['min' => static::COLUMNS_MIN, 'max' => static::COLUMNS_MAX, 'step' => 1])
                ->setLabel('Nombre de colonnes')
                ->setRequired()
                ->setDefaultValue($defaultColumns)
            ->addTrueFalse('add_headline', ['ui' => 1])
                ->setLabel('Ajouter une ligne d\'en tête');

        /**
         * Headline
         */
        $head = $field->addRepeater('head', ['min' => 1, 'max' => 1, 'layout' => 'table'])
                ->conditional('add_headline', '==', 1)
                ->setRequired()
                ->setLabel('Entête');

            foreach ($columns as $column) {
                $head->addFields($column);
            }

        $head->end();
        $field->addFields($head);

        /**
         * Body
         */
        $body = $field->addRepeater('body', ['min' => 1, 'rows_per_page' => 20, 'button_label' => 'Ajouter une ligne'])
            ->setLabel('Lignes')
            ->setRequired();

            foreach ($columns as $column) {
                $body->addFields($column);
            }
        $body->end();
        $field->addFields($body);

        return $field;
    }

    protected function getPlainHtml(array $parameters): string
    {
        // if ($this->templateExists()) {
        //     return parent::getPlainHtml($parameters);
        // }

        \ob_start();
        \extract($parameters);
        ?>
        <table>
            <?php if ($add_headline) : ?>
                <thead>
                    <tr>
                        <?php
                        for ($i = 1; $i <= $cols_number; $i++) :
                            printf('<th>%s</td>', $head[0]['column_' . $i]);
                        endfor;
                        ?>
                    </tr>
                </thead>
            <?php endif; ?>
            <tbody>
                <?php foreach ($body as $row) : ?>
                    <tr>
                        <?php
                        for ($i = 1; $i <= $cols_number; $i++) :
                            printf(
                                '<td><div aria-hidden="true">%s</div><span>%s</span></td>',
                                $add_headline ? $head[0]['column_' . $i] : '',
                                $row['column_' . $i]
                            );
                        endfor;
                        ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return \ob_get_clean();
    }

    public function toArray()
    {
        return [
            'cols_number' => $this->cols_number,
            'add_headline' => $this->add_headline,
            'head' => $this->head,
            'body' => $this->body,
        ];
    }
}
