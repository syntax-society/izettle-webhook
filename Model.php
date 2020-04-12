<?php

abstract class Model implements JsonSerializable {

	protected $container = [];

	abstract function getFieldSpecifications(): array;

	private function connectSql(array $config): mysqli {
		$servername = "localhost";
		$username = "pi";
		$password = "hallonfastpaengelska";
		$database = "syntaxsociety";

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$conn = new mysqli(
			$config['mysqlHostname'],
			$config['mysqlUsername'],
			$config['mysqlPassword'],
			$config['mysqlDatabase'],
		);

		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		return $conn;
	}

	private function disconnectSql(mysqli $conn): void {
		$conn->close();
	}

	private function getColumnNames(mysqli $conn, string $tablename): array {
		$stmt = $conn->prepare("select COLUMN_NAME from information_schema.columns where table_name = \"$tablename\"");
		$stmt->execute();
		$rows = $stmt->get_result();
		$column_specs = iterator_to_array($rows);
		$stmt->close();
		return array_map(function($value) {return $value['COLUMN_NAME'];}, $column_specs);
	}

	protected function performInsert(array $config, string $tablename, array $data): void {
		$conn = $this->connectSql($config);

		$column_names = $this->getColumnNames($conn, $tablename);
		$diff_elements = array_diff(array_keys($data), $column_names); // checks if POST and SQL column names are the same
		if (count($diff_elements) === 0) {
			$placeholders = str_repeat('?, ', count($data) - 1) . '?';
			$sql = "insert into $tablename values ($placeholders)";

			$types = str_repeat('s', count($data));
			$data = array_map(function($value) {if ($value === "") return NULL; else return $value;}, $data);

			$stmt = $conn->prepare($sql);
			$stmt->bind_param($types, ...array_values($data));
			$stmt->execute();
			$stmt->close();
		} else {
			throw new \Exception(
				'Provided incorrect data:' . PHP_EOL .
				json_encode($diff_elements, JSON_PRETTY_PRINT) . PHP_EOL .
				'Valid indices: ' . join(', ', $column_names) . PHP_EOL
			);
		}

		$this->disconnectSql($conn);
	}

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
