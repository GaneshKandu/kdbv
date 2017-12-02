<?php

namespace kanduganesh;

class db{
	
	public static $btree = null;
	
	public static function init($db){
		
		self::$btree = btree::open($db);
		
	}
	
	public static function set($key,$value){
		
		self::$btree->set($key,gzdeflate(serialize($value), 9));
		
	}
	
	public static function get($key){
		
		$return  = self::$btree->get($key);
		
		if(!$return){
			return false;
		}
		
		return unserialize(gzinflate($return));
		
	}
}