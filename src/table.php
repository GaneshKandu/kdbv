<?php

namespace kanduganesh;

use PDOException;

class tabledef{
	
	public $pdo = null;
	public $btree = null;
	public $pdodsn = null;
	public $sql = array();
	public $key = false;
	
	function setTables(){
		
		$stmt = $this->pdo->query('SHOW TABLES;');
		
		$row = $stmt->fetchall();

		$tables = array();

		foreach($row as $table){
			$tables[] = $table["Tables_in_{$this->pdodsn['DATABASE']}"];
		}
		
		db::set('table',$tables);
	}
	
	function getTables(){
		
		$stmt = $this->pdo->query('SHOW TABLES;');
		
		$row = $stmt->fetchall();

		$tables = array();

		foreach($row as $table){
			$tables[] = $table["Tables_in_{$this->pdodsn['DATABASE']}"];
		}
		
		return $tables;
	}
	
	function setTablestatus(){
		
		$stmt = $this->pdo->query('SHOW TABLE STATUS;');
		
		$row = $stmt->fetchall();
		
		$STATUS = array();
		
		foreach($row as $tblstat){
			$STATUS[] = array(
				'Name' => $tblstat['Name'],
				'Engine' => $tblstat['Engine'],
				'Auto_increment' => $tblstat['Auto_increment'],
				'Collation' => $tblstat['Collation'],
				'Comment' => $tblstat['Comment']
			);
		}
		
		db::set('.STATUS',$STATUS);
	}
	
	function addTablestatus(){
		
		$sql = array();
		
		$tblstat = db::get('.STATUS');
		
		$sql[] = "SET foreign_key_checks = 0;";

		foreach($tblstat as $stat){
			
			$temp = array();
			
			if(!empty($stat['Engine'])){
				$temp[] = "ENGINE = {$stat['Engine']}";
			}
			
			if(!empty($stat['Collation'])){
				$temp[] = "COLLATE  = {$stat['Collation']}";
			}
			
			if(!empty($stat['Comment'])){
				$stat['Comment'] = $this->quoteIdent($stat['Comment']);
				$temp[] = "COMMENT  = {$stat['Comment']}";
			}
			
			$sql[] = "ALTER TABLE {$stat['Name']} ". implode(' ',$temp) .";";
		}
		
		$sql[] = "SET foreign_key_checks = 1;";
		
		return $sql;
		
	}
	
	/*
	
	*/
	
	function prepare_index_array($keys){
		
		$array = array();
		
		foreach($keys as $key){
			if($key['Key_name'] == 'PRIMARY'){
				$array[$key['Table']]['PRIMARY'][] = array('NAME' => $key['Column_name'],'Sub_part' => $key['Sub_part']);
			}else{
				if($key['Non_unique']){
					$array[$key['Table']]['index'][] = array('NAME' => $key['Key_name'],'COLUMN' => $key['Column_name'],'Sub_part' => $key['Sub_part']);
				}else{
					$array[$key['Table']]['unique'][] = array('NAME' => $key['Key_name'],'COLUMN' => $key['Column_name'],'Sub_part' => $key['Sub_part']);
				}
			}
		}
		return $array;
	}
	
	/*
	ALTER TABLE `$table`
	ADD PRIMARY KEY (`$column`),
	ADD KEY `$indexname` (`$column`),
	ADD KEY `$indexname` (`$column`);
	*/
	
	function setIndexs(){
			
		$tables = db::get('table');
		
		$row = array();
		
		foreach($tables as $table){
			$stmt = $this->pdo->query("SHOW INDEX FROM $table;");
			$row[] = $stmt->fetchall();
		}
		
		$indexs = array();
		
		foreach($row as $keys){
			$temp = $this->prepare_index_array($keys);
			$indexs = array_merge($indexs,$temp);
		}
		db::set('index',$indexs);
	}
	
	function getIndexs(){
			
		$tables = $this->getTables();
		
		$row = array();
		
		foreach($tables as $table){
			$stmt = $this->pdo->query("SHOW INDEX FROM $table;");
			$row[] = $stmt->fetchall();
		}
		
		$indexs = array();
		
		foreach($row as $keys){
			$temp = $this->prepare_index_array($keys);
			$indexs = array_merge($indexs,$temp);
		}
		
		return $indexs;
	}
	
	function setTblDesc(){
		$td = array();
		$tables = db::get('table');
		foreach($tables as $table){
			$stmt = $this->pdo->query("SHOW FULL COLUMNS FROM $table;");
			$row = $stmt->fetchall();
			db::set($table,$this->TableDefinition($table,$row));
		}
	}
	
	function TableDefinition($table,$columns){
		$return = array();
		foreach($columns as $column){
			$return[$column['Field']] = array(
				'Type' => $column['Type'],
				'Null' => $column['Null'],
				'Default' => $column['Default'],
				'Extra' => $column['Extra'],
				'Collation' => $column['Collation'],
				'Comment' => $column['Comment'],
			);
			if($this->key){
				$return[$column['Field']]['Key'] = $column['Key'];
			}
		}
		return $return;
	}
	
	function setRelation(){
		$stmt = $this->pdo->query('
		SELECT 
		  `TABLE_SCHEMA`,                          -- Foreign key schema
		  `TABLE_NAME`,                            -- Foreign key table
		  `COLUMN_NAME`,                           -- Foreign key column
		  `REFERENCED_TABLE_SCHEMA`,               -- Origin key schema
		  `REFERENCED_TABLE_NAME`,                 -- Origin key table
		  `REFERENCED_COLUMN_NAME`,                -- Origin key column
		  `CONSTRAINT_NAME`                        -- constraint name
		FROM
		  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`  -- Will fail if user don\'t have privilege
		WHERE
		  `TABLE_SCHEMA` = SCHEMA()                -- Detect current schema in USE 
		  AND `REFERENCED_TABLE_NAME` IS NOT NULL; -- Only tables with foreign keys;
		');

		$row = $stmt->fetchall();

		db::set('.RELATIONS',$row);
	}
	
	function getRelation(){
		$stmt = $this->pdo->query('
		SELECT 
		  `TABLE_SCHEMA`,                          -- Foreign key schema
		  `TABLE_NAME`,                            -- Foreign key table
		  `COLUMN_NAME`,                           -- Foreign key column
		  `REFERENCED_TABLE_SCHEMA`,               -- Origin key schema
		  `REFERENCED_TABLE_NAME`,                 -- Origin key table
		  `REFERENCED_COLUMN_NAME`,                -- Origin key column
		  `CONSTRAINT_NAME`                        -- constraint name
		FROM
		  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`  -- Will fail if user don\'t have privilege
		WHERE
		  `TABLE_SCHEMA` = SCHEMA()                -- Detect current schema in USE 
		  AND `REFERENCED_TABLE_NAME` IS NOT NULL; -- Only tables with foreign keys;
		');

		return $stmt->fetchall();
	}
	
	function generateNullCommand($defaultValue) {
		return ($defaultValue == 'NO') ? 'NOT NULL' : 'NULL';
	}
	
	function generateCollation($collation){
		
		if(!empty($collation)){
			return " COLLATE {$collation} ";
		}
		return '';
	}
	
	function generateDefaultCommand($definitions){
/* 
		if ($definitions['Extra'] == 'auto_increment') {
			return "AUTO_INCREMENT";
		}
*/
		if (in_array($definitions['Default'], array('CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))) {
			return "DEFAULT {$definitions['Default']}";
		}

		if ($definitions['Default'] != '' and strpos($definitions['Type'], 'int') !== false) {
			return "DEFAULT {$definitions['Default']}";
		}

		if ($definitions['Default'] != '' and strpos($definitions['Type'], 'decimal') !== false) {
			return "DEFAULT {$definitions['Default']}";
		}

		if ($definitions['Default'] != '') {
			return "DEFAULT '{$definitions['Default']}'";
		}

		if ($definitions['Null'] == 'YES' and $definitions['Default'] == '') {
			return "DEFAULT NULL";
		}

		if ($definitions['Null'] == 'NO' and $definitions['Default'] == '' and strpos(strtolower($definitions['Type']), 'char') !== false) {
			return '';//return "DEFAULT ''";
		}

		if ($definitions['Null'] == 'NO' and $definitions['Default'] == '' and strpos(strtolower($definitions['Type']), 'int') !== false) {
			return '';//return "DEFAULT 0";
		}

		return '';
	}
	
	function create_table($ctable){
		$entries = array();
		$primaryKey = array();
		$keys = array();
		foreach (db::get($ctable) as $columnName => $definitions) {
			$entries[] = '`' . $columnName . '` ' . $definitions['Type'] . ' '. $this->generateCollation($definitions['Collation']) .' ' . $this->generateNullCommand($definitions['Null']) . ' ' . $this->generateDefaultCommand($definitions);

			if ($definitions['Key'] == 'PRI') {
				$primaryKey[] = $columnName;
			}

			if ($definitions['Key'] == 'MUL') {
				$keys[] = $columnName;
			}
		}

		if (count($primaryKey) > 0) {
			$entries[] = 'PRIMARY KEY (`' . implode('`,`', $primaryKey) . '`)';
		}

		foreach ($keys as $key) {
			$entries[] = 'KEY (`' . $key . '`)';
		}

		return "CREATE TABLE IF NOT EXISTS `{$ctable}` (" . implode(',', $entries) . ");";
	}
	
	function drop_table($table){
		return "DROP TABLE $table;";
	}
	
	function getQuery(){
		$sql = array();
		$tables = $this->getTables();
		$ctables = array_diff(db::get('table'),$tables);
		$dtables = array_diff($tables,db::get('table'));

		foreach($ctables as $ctable){
			$sql[] = $this->create_table($ctable);
		}

		foreach($dtables as $table){
			$sql[] = $this->drop_table($table);
		}
		return $sql;
	}
	
	function column_defination($column){
			
		$return = " {$column['Type']}";
		
		if(!empty($column['Collation'])){
			$return .= " COLLATE {$column['Collation']} ";
		}
		
		if($column['Null'] == 'NO'){
			$return .= " NOT NULL ";
		}else{
			$return .= " NULL ";
		}
		
		$return .= $this->generateDefaultCommand($column);
		
		$return .= "\n";
		
		return $return;
	}
	
	function getAlters(){
		
		$sql = array();
		
		$tables = $this->getTables();
		
		foreach($tables as $key => $table){
			$stmt = $this->pdo->query("SHOW FULL COLUMNS FROM $table;");
			$row = $stmt->fetchall();
			$ncolumn = db::get($table);
			if($ncolumn){
				$ocolumn = $this->TableDefinition($table,$row);
				$add_column = array_diff(array_keys($ncolumn),array_keys($ocolumn));
				$drop_column = array_diff(array_keys($ocolumn),array_keys($ncolumn));
				$mod_column = array_intersect(array_keys($ocolumn),array_keys($ncolumn));
				
				foreach($add_column as $column){
					$sql[] = "ALTER TABLE $table ADD COLUMN `$column` ".$this->column_defination($ncolumn[$column]).";";
				}
				
				foreach($drop_column as $column){
					$sql[] = "ALTER TABLE $table DROP COLUMN `$column`;";
				}
				
				$modify = array();
				
				foreach($mod_column as $column){
					$modify1 = array_diff($ncolumn[$column],$ocolumn[$column]);
					$modify2 = array_diff($ocolumn[$column],$ncolumn[$column]);
					$modify = array_merge($modify1,$modify2);
					if(count($modify)){
						$sql[] = "ALTER TABLE $table MODIFY COLUMN `$column`".$this->column_defination($ncolumn[$column]).";";
					}
				}
			}
		}
		return $sql;
	}
	
	function dropKeys(){
		$return = array();
		
		$constraints = $this->getRelation();
		
		foreach($constraints as $constraint){
			
			$return[] = "ALTER TABLE `{$constraint['TABLE_NAME']}` DROP FOREIGN KEY `{$constraint['CONSTRAINT_NAME']}`;";

		}

		return array_values($return);
	}

	function tilde($texts){
		$return  = array();
		foreach($texts as $text){
			$return[] = "`$text`";
		}
		return $return;
	}
	
	/*
	ALTER TABLE `$table`
	ADD PRIMARY KEY (`$column`),
	ADD KEY `$indexname` (`$column`),
	ADD KEY `$indexname` (`$column`);
	*/
	
	function addKeys(){
		$sql = array();
		$indexs = db::get('index');
		foreach($indexs as $table => $index){
			$temp = "ALTER TABLE `$table`";
			$_temp = array();
			//ADDING PRIMARY KEY
			if(isset($index['PRIMARY'])){
				$_primary = array();
				foreach($index['PRIMARY'] as $primary){
					$sub_part = empty($primary['Sub_part'])?'':"({$primary['Sub_part']})";
					$_primary[] = "`{$primary['NAME']}`$sub_part";
				}
				$columns = implode(",",$_primary);
				$_temp[] = "ADD PRIMARY KEY ({$columns})";
			}
			//ADDING INDEX KEY
			if(isset($index['index'])){
				$index_temp = array();
				$sub_part = array();
				foreach($index['index'] as $key){
					$index_temp[$key['NAME']][] = $key['COLUMN'];
					$sub_part[] = empty($key['Sub_part'])?'':"({$key['Sub_part']})";
				}
				foreach($index_temp as $name => $_keys){
					$_indexs = array();
					foreach($this->tilde($_keys) as $no => $val){
						$_indexs[] = "$val{$sub_part[$no]}";
					}
					$_temp[] = "ADD KEY `{$name}` (".implode(",",$_indexs).")";
				}
			}
			//ADDING INDEX UNIQUE KEY
			if(isset($index['unique'])){
				$index_temp = array();
				$sub_part = array();
				foreach($index['unique'] as $key){
					$index_temp[$key['NAME']][] = $key['COLUMN'];
				$sub_part[] = empty($key['Sub_part'])?'':"({$key['Sub_part']})";
				}
				foreach($index_temp as $name => $_keys){
					$_indexs = array();
					foreach($this->tilde($_keys) as $no => $val){
						$_indexs[] = "$val{$sub_part[$no]}";
					}
					$_temp[] = "ADD UNIQUE KEY `{$name}` (".implode(",",$_indexs).")";
				}
			}
			
			$sql[] = $temp." ".implode(",",$_temp).";";
			
		}
		return $sql;
	}
	
	function add_auto_increment(){
		$return = array();
		$tables = db::get('table');
 		foreach($tables as $table){
			$column = db::get($table);
			if($row = $this->check_autoincreate($column)){
				$return[] = "ALTER TABLE `{$table}` MODIFY `{$row['NAME']}` {$row['Type']} NOT NULL AUTO_INCREMENT;";
			}
		} 
		return $return;
	}
	
	/*
	ALTER TABLE `[TABLE_NAME]`
	ADD CONSTRAINT `[CONSTRAINT_NAME]` FOREIGN KEY (`[COLUMN_NAME]`) REFERENCES `[REFERENCED_TABLE_NAME]` (`[REFERENCED_COLUMN_NAME]`);
	*/
	
	function addRelations(){
		$sql = array();
		$rels = db::get('.RELATIONS');
		foreach($rels as $rel){
			$sql[] = "ALTER TABLE `{$rel['TABLE_NAME']}`
ADD CONSTRAINT `{$rel['CONSTRAINT_NAME']}` FOREIGN KEY (`{$rel['COLUMN_NAME']}`) REFERENCES `{$rel['REFERENCED_TABLE_NAME']}` (`{$rel['REFERENCED_COLUMN_NAME']}`);";
		}
		
		return $sql;
	}
	
	/**
	 *
	 * IS TABLE CONTAIN PRIMARY KEY
	 *
	 * @param    TABLE $array
	 * @return   boolean
	 *
	 */
	 
	function check_unique_key($columns){
		foreach($columns as $name => $column){
			if($column['Key'] == 'MUL' || $column['Key'] == 'UNI'){
				$column['NAME'] = $name;
				return $column;
			}
		}
		return false;
	}
	
	/**
	 *
	 * IS TABLE CONTAIN PRIMARY KEY
	 *
	 * @param    TABLE $array
	 * @return   boolean
	 *
	 */
	 
	function check_primary_key($columns){
		foreach($columns as $column){
			if($column['Key'] == 'PRI'){
				return true;
			}
		}
		return false;
	}
	
	/**
	 *
	 * IS TABLE CONTAIN AUTO_INCREMENT
	 *
	 * @param    TABLE $array
	 * @return   boolean
	 *
	 */
	
	function check_autoincreate($columns){
		foreach($columns as $name => $column){
			if($column['Extra'] == 'auto_increment'){
				$column['NAME'] = $name;
				return $column;
			}
		}
		return false;
	}
	
	function push($A1,$A2){
		foreach($A2 as $A){
			$A1[] = $A;
		}
		return $A1;
	}
	
	function execute($queries){
		foreach($queries as $query){
			try{
				$stmt = $this->pdo->prepare($query);
				$stmt->execute(array());
			}catch (PDOException $e) {
 				//echo "$query\n";
				//echo $e->getCode();
				//echo "\n";
				//echo $e->getMessage();
				//echo "\n";
			} 
		}
	}
	
	function dropIndexs(){
	
		$indexs = $this->getIndexs();
		$sql = array();
		
		//droping key index
		foreach($indexs as $table => $index){
			if(isset($index['index'])){
				foreach($index['index'] as $keys){
					// key to reduce dublicate query
					$sql[$table.'_0_'.$keys['NAME']] = "ALTER TABLE `{$table}` DROP INDEX `{$keys['NAME']}`;";
				}
			}
		}
		//droping unique key index
		foreach($indexs as $table => $index){
			if(isset($index['unique'])){
				foreach($index['unique'] as $keys){
					// key to reduce dublicate query
					$sql[$table.'_1_'.$keys['NAME']] = "ALTER TABLE `{$table}` DROP INDEX `{$keys['NAME']}`;";
				}
			}
		}
		//droping primary key
		foreach($indexs as $table => $index){
			if(isset($index['PRIMARY'])){
				//modifing column to override not increament column structure to drop auto increament
				$stmt = $this->pdo->query("SHOW FULL COLUMNS FROM $table;");
				$row = $stmt->fetchall();
				if($column = $this->check_autoincreate($this->TableDefinition($table,$row))){
					$sql[$table.'_0_'.$row['Field']] = "ALTER TABLE `{$table}` MODIFY `{$column['NAME']}` INT;";
				}
				//droping primary key
				foreach($index['PRIMARY'] as $keys){
					// key to reduce dublicate query
					$sql[$table.'_2'] = "ALTER TABLE `{$table}` DROP PRIMARY KEY;";
				}
			}
		}
		
		return array_values($sql);
	}
	
	function quoteIdent($field) {
		return "`".str_replace("`","``",$field)."`";
	}

}