<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * Add settings fields to FieldsBuilder object.
 */
trait Settings
{
    protected array $settings = [];
    protected array $lockSettings = [];

    /**
     * Add settings callable to the stack.
     * The settings callable has to provide a FieldsBuilder object. It will be append to the settings accordion field.
     */
    public function addSettings(callable $provider, int $priority = 10): self
    {
        if (!\is_callable($provider, true, $provider_name)) {
            return $this;
        }

        if (\in_array($provider_name, $this->lockSettings)) {
            return $this;
        }

        $this->settings[$priority][] = $provider;
        return $this;
    }

    /**
     * Search and remove settings already set
     * @param callable $provider The callable to remove
     * @param int $priority The priority wich the callable was registered
     * @param int $lock Lock this setting to prevent register again
     */
    public function removeSettings(callable $provider, int $priority = 10, bool $lock = false): self
    {
        if (!\is_callable($provider, true, $provider_name)) {
            return $this;
        }

        foreach ($this->settings[$priority] as $i => $provider) {
            if (\is_callable($provider, true, $callable_name) && $provider_name === $callable_name) {
                unset($this->settings[$priority][$i]);
            }
        }

        if ($lock) {
            $this->lockSettings($provider_name);
        }

        return $this;
    }

    /**
     * Prevent settings callable to be registered in the future
     */
    public function lockSettings(callable|string $provider): self
    {
        if (\is_string($provider)) {
            $provider_name = $provider;
        } elseif (!\is_callable($provider, true, $provider_name)) {
            return $this;
        }

        $this->lockSettings[] = $provider_name;
        return $this;
    }

    public function removeAllSettings(): self
    {
        $this->settings = [];
        return $this;
    }

    public function fieldSettingsName(): string
    {
        return $this->getName() . '_settings';
    }

    /**
     * Accordion settings name
     */
    public function fieldSettingsTitle(): string
    {
        return __('Paramètres du bloc ' . lcfirst($this->getLabel()), app()->get('settings')['text-domain']);
    }

    /**
     * Add settings fields on existings fieldgroup;
     */
    protected function applySettings(FieldsBuilder $field): FieldsBuilder
    {
        \ksort($this->settings, SORT_NUMERIC);
        foreach ($this->settings as $priority => $callables) {
            foreach ($callables as $callable) {
                $field->addFields($callable());
            }
        }

        return $field;
    }

    /**
     * Provide an existings fieldgroup and create an accordion field if missing and append settings fields;
     */
    public function useSettingsOn(FieldsBuilder $field): self
    {
        if (empty($this->settings)) {
            return $this;
        }

        $accordion = $this->fieldSettingsName() . '_accordion';
        if (!$field->fieldExists($accordion)) {
            $field->addAccordion($this->fieldSettingsName(), ['label' => $this->fieldSettingsTitle()]);
        } else {
            $field->getField($accordion);
        }

        $this->applySettings($field);

        return $this;
    }
}
