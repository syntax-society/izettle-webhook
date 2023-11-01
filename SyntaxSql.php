<?php



class SyntaxSql {

	private $conn;

	function __construct(array $config) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$this->conn = new mysqli(
			$config['mysql_hostname'],
			$config['mysql_username'],
			$config['mysql_password'],
			$config['mysql_database'],
		);

		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
	}

	function __destruct() {
		$this->conn->close();
	}

	private function getColumnNames(string $tablename): array {
		$stmt = $this->conn->prepare("select COLUMN_NAME from information_schema.columns where table_name = \"$tablename\"");
		$stmt->execute();
		$rows = $stmt->get_result();
		$column_specs = iterator_to_array($rows);
		$stmt->close();
		return array_map(function($value) {return $value['COLUMN_NAME'];}, $column_specs);
	}

	private function getPrimaryColumnNames(string $tablename): array {
		$stmt = $this->conn->prepare("show index from $tablename where Key_name = 'PRIMARY';");
		$stmt->execute();
		$rows = $stmt->get_result();
		$column_specs = iterator_to_array($rows);
		$stmt->close();
		return array_map(function($value) {return $value['Column_name'];}, $column_specs);
	}

	public function performInsert(string $tablename, array $data): bool {
		$this->conn->ping();

		$column_names = $this->getColumnNames($tablename);

		$diff_elements = array_diff(array_keys($data), $column_names); // checks if POST and SQL column names are the same
		if (count($diff_elements) === 0) {
			$placeholders = str_repeat('?, ', count($data) - 1) . '?';
			$sql = "insert into $tablename values ($placeholders)";

			$types = str_repeat('s', count($data));
			$data = array_map(function($value) {if ($value === "") return NULL; else return $value;}, $data);

			$stmt = $this->conn->prepare($sql);
			$stmt->bind_param($types, ...array_values($data));

			$isInserted = $stmt->execute();
			$stmt->close();
		} else {
			throw new \Exception(
				'Provided incorrect data:' . PHP_EOL .
				json_encode($diff_elements, JSON_PRETTY_PRINT) . PHP_EOL .
				'Valid indices: ' . join(', ', $column_names) . PHP_EOL
			);
		}

		return $isInserted;
	}

	public function performDelete(string $tablename, array $data): bool {
		$this->conn->ping();

		$column_names = $this->getColumnNames($tablename);

		$diff_elements = array_diff(array_keys($data), $column_names); // checks if POST and SQL column names are the same
		if (count($diff_elements) === 0) {
			$primary_column_names = $this->getPrimaryColumnNames($tablename);

			$primary_key_values = array_values(array_intersect_key($data, array_flip($primary_column_names)));

			$post_where = array_map(function($value) { return $value . '=(?)'; }, $primary_column_names);
			$post_where = join(' and ', $post_where);

			$sql = "delete from $tablename where $post_where";
			$stmt = $this->conn->prepare($sql);

			$types = str_repeat('s', count($primary_column_names));
			$stmt->bind_param($types, ...$primary_key_values);

			$isDeleted = $stmt->execute();
			$stmt->close();
		} else {
			throw new \Exception(
				'Provided incorrect data:' . PHP_EOL .
				json_encode($diff_elements, JSON_PRETTY_PRINT) . PHP_EOL .
				'Valid indices: ' . join(', ', $column_names) . PHP_EOL
			);
		}

		return $isDeleted;
	}

	public function performUpdate(string $tablename, array $data): bool {
		$this->conn->ping();

		$column_names = $this->getColumnNames($tablename);

		$diff_elements = array_diff(array_keys($data), $column_names); // checks if POST and SQL column names are the same
		if (count($diff_elements) === 0) {
			$primary_column_names = $this->getPrimaryColumnNames($tablename);

			$primary_key_values = array_values(array_intersect_key($data, array_flip($primary_column_names)));
			// create the list of new values like: key1="value1", key2="value2"
			array_walk($data, function(&$value, $key) {$value = "$key=\"$value\""; });
			$new_values = join(", ", $data);

			// create the where-specification like: key1=(?) and key2=(?)
			$post_where = array_map(function($value) { return $value . '=(?)'; }, $primary_column_names);
			$post_where = join(" and ", $post_where);

			$sql = "update $tablename set $new_values where $post_where";
			$stmt = $this->conn->prepare($sql);

			$types = str_repeat('s', count($primary_column_names));
			$stmt->bind_param($types, ...$primary_key_values);

			$stmt->execute();
			$stmt->close();
		} else {
			throw new \Exception(
				'Provided incorrect data:' . PHP_EOL .
				json_encode($diff_elements, JSON_PRETTY_PRINT) . PHP_EOL .
				'Valid indices: ' . join(', ', $column_names) . PHP_EOL
			);
		}
	}
}
