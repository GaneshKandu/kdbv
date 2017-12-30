<?php

namespace kanduganesh;

class db{
	
	public static $btree = null;
	public static $notation = array();
	
	public static function init($db){
		
		self::$btree = btree::open($db);
		
	}
	
	public static function set($key,$value){
		
		if(!empty(self::$notation['|:prefix:|'])){
			$value = json_decode(strtr(json_encode($value),array_flip(self::$notation)),1);
			$key = json_decode(strtr(json_encode($key),array_flip(self::$notation)),1);
		}
		
		self::$btree->set($key,gzdeflate(serialize($value), 9));
		
	}
	
	public static function get($key){
		
		if(!empty(self::$notation['|:prefix:|'])){
			$key = json_decode(strtr(json_encode($key),array_flip(self::$notation)),1);
		}
		
		$return  = self::$btree->get($key);
		
		if(!$return){
			return false;
		}
		
		$return = unserialize(gzinflate($return));
		
		if(!isset(self::$notation['|:prefix:|'])){
			self::$notation['|:prefix:|'] = '';
		}
		
		return json_decode(strtr(json_encode($return),self::$notation),1);
	}
}