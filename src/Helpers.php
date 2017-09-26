<?php
namespace App;

use Monolog\Logger;

class Helpers {

	// Check results
	// if no data, return []
	public static function createResults($list, $select, $converter = []){
		$list = isset($list['warning'])? []: $list;

 		// If no data, return []
 		if (!count($list)) return [];

	    $data = [];

	    foreach ($list as $item) {
	        $attributes = [];

	        foreach ($select as $table_column => $attribute) {
	            $split_lookup = explode(';#', isset($item[$table_column])? $item[$table_column]: '');
	            
	            if (count($converter) && isset($converter[$attribute])) {
	            	if ($converter[$attribute] === DATA_TYPE_INTEGER) {
						$split_lookup[0] = intval($split_lookup[0]);
	            	}
	            }

	            $attributes[$attribute] = $split_lookup[0];
	        }

	        $data[] = $attributes;
	    }
	  	return $data;
 	}

 	// Create LOV
	public static function createLOV($list, $columns, $table_key = "ID", $converter = []){
 		$lov = [];

 		$list = isset($list['warning'])? []: $list;

 		// If no data, return []
 		if (!count($list)) return [];

 		foreach ($list as $item) {
 			$attributes = [];

 			foreach ($columns as $table_column => $attribute) {
 					$split_lookup = explode(';#', isset($item[$table_column])? $item[$table_column]: '');

 					if ($split_lookup[0] === 'float') {
 						$split_value = explode('.', $split_lookup[1]);

 						$attributes[$attribute] = $split_value[0];
 					} else {
 						$attributes[$attribute] = $split_lookup[0];
 					}

 					// Convert value into data type integer
					if (count($converter) && isset($converter[$attribute])) {
						if ($converter[$attribute] === DATA_TYPE_INTEGER) {
							$attributes[$attribute] = intval($attributes[$attribute]);
						}
					}
 			}

 			$split_key = explode(';#', isset($item[$table_key])? $item[$table_key]: '');

 			$lov[$split_key[0]] = $attributes;
 		}


 		return $lov;
 	}

 	// Create response
 	public static function createResponse ($status, $messsage, $data = []) {
		$response = [
 			"meta" => [
 				"status" => $status,
 				"message" => $messsage,
 			]
 		];
		
		// Add response data
		if (count($data)) {
			$response['data'] = $data;
		}

 		return $response;
 	}

 	// Create logger
 	public static function createLogger ($logger, $type, $function_name, $message) {
 		// $logger = new Logger();
 		switch ($type) {
 			case LOGGER_ERROR:
 				$logger->error($function_name . ": " . $message);
 				break;
 			case LOGGER_WARNING:
 				$logger->warning($function_name . ": " . $message);
 				break;
 			default:
 				$logger->info($function_name . ": " . $message);
 				break;
 		}
 	}


 	public static function getParentByLevel ($level, $id, $table_peraturan) {
        if (((int)$table_peraturan[$id]['level'] - 1) < $level){
            return null;
        } else if ($level === ((int)$table_peraturan[$id]['level'] - 1)){
            return $table_peraturan[$id]['id_parent'];
        } else {
            return self::getParentByLevel($level, $table_peraturan[$id]['id_parent'], $table_peraturan);
        }
    }

 }