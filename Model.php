<?php

abstract class Model implements JsonSerializable {

	protected $container = [];

	abstract function getFieldSpecifications(): array;

	function __construct($data = []) {
		foreach ($this->getFieldSpecifications() as $fieldName => $fieldType) {
			if (!isset($data[$fieldName])) {
				$this->container[$fieldName] = null;
				continue;
			}

			$is_array = substr($fieldType, -2) === '[]';
			$className = $is_array ? substr($fieldType, 0, strlen($fieldType) - 2) : $fieldType;

			if (class_exists($className)) {
				if ($is_array) {
					$arr = [];
					foreach ($data[$fieldName] as $arrayObjectData) {
						array_push($arr, new $className($arrayObjectData));
					}
					$this->container[$fieldName] = $arr;
				} else {
					$this->container[$fieldName] = new $className($data[$fieldName]);
				}
			} else {
				$this->container[$fieldName] = $data[$fieldName];
			}
		}
	}

	public function __isset(string $fieldName): bool {
		return array_key_exists($fieldName, $this->container);
	}

	public function __unset(string $fieldName): void {
		unset($this->container[$fieldName]);
	}

	public function __get(string $fieldName) {
		if (!array_key_exists($fieldName, $this->container)) {
			return null;
		}
		return $this->container[$fieldName];
	}

	public function __set($fieldName, $data): void {
		if (!array_key_exists($fieldName, $this->getFieldSpecifications())) {
			return;
		}

		$fieldType = $this->getFieldSpecifications()[$fieldName];
		$is_array = substr($fieldType, -2) === '[]';
		$className = $is_array ? substr($fieldType, 0, strlen($fieldType) - 2) : $fieldType;

		if ($is_array && !is_array($data)) {
			throw new \Exception('Value must be array');
		}

		if (class_exists($className)) {
			if ($is_array) {
				$arr = [];
				foreach ($data as $arrayObjectData) {
					array_push($arr, new $className($arrayObjectData));
				}
				$this->container[$fieldName] = $arr;
			} else {
				$this->container[$fieldName] = new $className($data);
			}
		} else {
			$this->container[$fieldName] = $data;
		}
	}

	public function jsonSerialize() {
		return $this->container;
	}

	public function __toString() {
		return json_encode($this, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
	}
}
