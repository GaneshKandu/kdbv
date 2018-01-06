<?php

/**
 * mysql database auto schema migration tool 
 *
 * @author     Ganesh Kandu <kanduganesh@gmail.com>
 * @copyright  2016-2017 Ganesh Kandu
 * <https://github.com/GaneshKandu/kdbv>
 */
 
namespace kanduganesh;

use PDO;

class kdbv extends tabledef{
	
	function __construct($pdodsn){
		
		db::$notation['|:prefix:|'] = $pdodsn['PREFIX'];
		
		$this->init($pdodsn);
	}
	
	function init($pdodsn){
		
		$this->pdodsn = $pdodsn;
		
		
		$charset = 'utf8mb4';
		
		$dsn = "mysql:host={$pdodsn['HOST']};port={$pdodsn['PORT']};dbname={$pdodsn['DATABASE']};charset=$charset";
		
		$opt = array(
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		);

		$this->pdo = new PDO($dsn, $pdodsn['USER'], $pdodsn['PASS'], $opt);
	}
	
	function make(){
		
		if(file_exists($this->pdodsn['KDBV'])){
			unlink($this->pdodsn['KDBV']);
		}
		db::init($this->pdodsn['KDBV']);
		$this->setTables();
		$this->setIndexs();
		$this->setTblDesc();
		$this->setRelation();
		$this->setTablestatus();
	}
	
	function upgrade(){
		
		db::init($this->pdodsn['KDBV']);
		$queries = $this->query();
		$this->execute($queries);
	}
	
	function query(){
		
		db::init($this->pdodsn['KDBV']);
		$sql = array();
		$sql = $this->push($sql , $this->dropKeys());
		$sql = $this->push($sql , $this->dropIndexs());
		$sql = $this->push($sql , $this->getQuery());
		$sql = $this->push($sql , $this->getAlters());
		$sql = $this->push($sql , $this->addKeys());
		$sql = $this->push($sql , $this->addTablestatus());
		$sql = $this->push($sql , $this->add_auto_increment());
		$sql = $this->push($sql , $this->addRelations());
		return $sql;
	}
	
}