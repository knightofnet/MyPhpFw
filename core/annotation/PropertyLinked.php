<?php

namespace myphpfw\core\annotation;

/**
 * @Annotation
 */
class PropertyLinked
{
    private ?string $propertyName = null;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->propertyName = $values['value'];
        }
    }

    /**
     * @return string|null
     */
    public function getPropertyName(): ?string
    {
        return $this->propertyName;
    }


}