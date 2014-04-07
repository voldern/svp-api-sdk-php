<?php
/**
 * This file is part of the SVP API PHP client package
 *
 * @author
 * @copyright VG
 */

namespace SvpApi\Entity;

/**
 * SVP EntityAbstract Entity
 *
 * @author
 * @copyright VG
 */
abstract class EntityAbstract implements \JsonSerializable {
    /**
     * Construct and set properties from given array
     *
     * @param $properties
     */
    public function __construct($properties = []) {
        foreach ($properties as $key => $value) {
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
    }

    /**
     * Fill entity properties with data
     *
     * @param array $data
     */
    public function exchangeArray($data) {
        foreach ($this->getEntityProperties() as $property) {
            if (isset($data[$property])) {
                $this->{'set' . ucfirst($property)}($data[$property]);
            }
        }
    }

    /**
     * Get array containing entity values
     *
     * @return array
     */
    public function getArrayCopy() {
        $output = [];
        foreach ($this->getEntityProperties() as $property) {
            $output[$property] = $this->{'get' . ucfirst($property)}();
        }
        return $output;
    }

    /**
     * Enable the object to be serialized properly on json_encode invocation
     */
    public function jsonSerialize() {
        return $this->getArrayCopy();
    }

    /**
     * Get list of class' properties
     *
     * @return array
     */
    private function getEntityProperties() {
        return array_keys(get_object_vars($this));
    }

    /**
     * Provide basic getters/setters for properties existing within entity classes 
     */
    public function __call($name, $arguments) {
        $prefix = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));
        if ($prefix == 'set') {
            if (property_exists($this, $property)) {
                $this->$property = $arguments[0];
            }
        } else if ($prefix == 'get') {
            if (property_exists($this, $property)) {
                return $this->$property;
            } else {
                trigger_error('Undefined property "' . get_class($this) . '::' . $property . '"',
                    E_USER_NOTICE);
            }
        } else {
            trigger_error('Undefined method "' . get_class($this) . '::' . $name . '()"',
                E_USER_NOTICE);
        }
    }
}
