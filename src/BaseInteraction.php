<?php

namespace PayKassa;

use InvalidArgumentException;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;

class BaseInteraction
{
    public function getInteractionObject($fieldName = ''): ?object
    {
        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function fill(array $content): self
    {
        $modelFields = get_object_vars($this);

        foreach ($modelFields as $key => $field) {
            if (!isset($content[$key])) {
                continue;
            }

            $value = $content[$key];

            if (!is_array($value)) {
                $this->fillProperty($key, $value);
            } elseif ($this->{$key} instanceof Container) {
                $this->{$key} = $this->fillContainer($key, $value);
            } else {
                $this->{$key} = $this->fillArray($key, $value);
            }
        }

        return $this;
    }

    private function fillArray(int|string $key, array $array): array|null|object
    {
        $interactionObject = $this->getInteractionObject($key);
        if (!$interactionObject) {
            // If there is no InteractionObject, then we assume that it is an array of strings
            return $array;
        }

        return $interactionObject->fill($array);
    }

    /**
     * @throws ReflectionException
     */
    public function fillProperty($key, $value): void
    {
        if (!$this->isEnum($key)) {
            $this->{$key} = $value;

            return;
        }

        $reflectionProperty = new ReflectionProperty($this, $key);
        $type = $reflectionProperty->getType()->getName();

        if (!enum_exists($type)) {
            return;
        }

        $enumCases = $type::cases();
        foreach ($enumCases as $case) {
            if ($case->value === $value) {
                $this->{$key} = $case;

                return;
            }
        }

        throw new InvalidArgumentException("Значение '$value' не является допустимым для enum '$type'.");
    }

    protected function fillContainer(string $key, array $array): object
    {
        $container = $this->getInteractionObject($key);

        foreach ($array as $item) {
            $containerItemObject = $container->getInteractionObject();
            $containerItemObject->fill($item);
            $container->add($containerItemObject);
        }

        return $container;
    }

    /**
     * @throws ReflectionException
     */
    protected function isEnum(string $key): bool
    {
        $reflectionProperty = new ReflectionProperty($this, $key);
        $type = $reflectionProperty->getType();

        return $type instanceof ReflectionNamedType && enum_exists($type->getName());
    }
}
