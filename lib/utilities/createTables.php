#!/usr/bin/php
<?php
	function createTables($schemaDir, $host, $username, $password)
	{
		// Connect to MySQL and open the database
		$link = mysql_connect($host, $username, $password) 
			or die('Could not not connect to MySQL, error: ' . mysql_error() . '\n');
		echo "Connected Successfully\n";
		
		if ($handle = opendir($schemaDir)) 
		{
	 		while($filename = readdir($handle))
	 		{
	 			$extension = strtolower(substr(strrchr($filename, '.'), 1)); 
				if($extension === 'xml')
				{
	 				print("Processing $schemaDir/$filename\n");
					$queryList = createDatabase($schemaDir.'/'.$filename);
					foreach($queryList[1] as $query)
					{
						mysql_query($query, $link);
						//print("$query\n");
					}
					mysql_select_db($queryList[0]) or die('Could not open database: ' . $databaseName);
					$queryList = parseSchema($schemaDir.'/'.$filename);
					foreach($queryList as $query)
					{
						if (!mysql_query($query, $link))
							die("Query failed:\n" . $query . "\n" . mysql_error() . "\n");
						//print("$query\n");
					}
				}
	 		} 
 			closedir($handle);  
		}
	}

	function createDatabase($filename)
	{
		$xml = simplexml_load_file($filename);
		$dbName = $xml['Name'];
		$queryArray[] = "DROP DATABASE IF EXISTS $dbName";
		$queryArray[] = "CREATE DATABASE $dbName";
		return array($dbName, $queryArray);
	}

	function parseSchema($filename)
	{
		$xml = simplexml_load_file($filename);
		$dbName = $xml['Name'];
		$queryArray = array();
		
		foreach($xml->Table as $table)
		{
			$tablename = $table['Name'];
			$pKey = strval($table['PrimaryKey']);
			$columnStringList = array();
			
			// Create columns
			foreach($table->Column as $column)
			{
				$columnString = $column['Name'] . ' ' . $column['Type'] .
					((strval($column['Nullable']) === 'False') ? ' NOT NULL' : '') .
					((strval($column['Name']) === $pKey) ? ' AUTO_INCREMENT PRIMARY KEY' : '');
				$columnStringList[] = $columnString;
			}
			
			// Create foreign key constraints
			foreach($table->ForeignKey as $foreignKey)
			{
				$columnString = "FOREIGN KEY (" . $foreignKey['Name'] . ")" .
					" REFERENCES " . $foreignKey['Table'] . "(" . $foreignKey['Column'] . ")" .
					" ON DELETE " . strtoupper($foreignKey['OnDelete']) .
					" ON UPDATE " . strtoupper($foreignKey['OnUpdate']);
				$columnStringList[] = $columnString;
			}
			
			$queryArray[] = "SET FOREIGN_KEY_CHECKS = 0";
			$queryArray[] = "CREATE TABLE $tablename (\n\t" . implode(",\n\t", $columnStringList) . "\n) ENGINE=INNODB";
			$queryArray[] = "SET FOREIGN_KEY_CHECKS = 1";
		}
		return $queryArray;
	}

	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	createTables('db-schema', $init->getProp('Catalog_Host'), $init->getProp('Catalog_Username'), $init->getProp('Catalog_Password'));
?>
