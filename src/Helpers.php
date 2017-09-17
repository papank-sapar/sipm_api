<?php
namespace App;

use Monolog\Logger;

class Helpers {

	// Check results
	// if no data, return []
	public static function createResults($list, $select){
		$list = isset($list['warning'])? []: $list;

 		// If no data, return []
 		if (!count($list)) return [];

	    $data = [];

	    foreach ($list as $item) {
	        $attributes = [];

	        foreach ($select as $field => $attribute) {
	            $split_lookup = explode(';#', isset($item[$field])? $item[$field]: '');

	            $attributes[$attribute] = $split_lookup[0];
	        }

	        $data[] = $attributes;
	    }
	  	return $data;
 	}

 	// Create LOV
	public static function createLOV($list, $column, $table_key = "ID"){
 		$lov = [];

 		$list = isset($list['warning'])? []: $list;

 		// If no data, return []
 		if (!count($list)) return [];

 		foreach ($list as $item) {
 			$attributes = [];

 			foreach ($column as $table_column => $attribute) {
 					$split_lookup = explode(';#', isset($item[$table_column])? $item[$table_column]: '');
					
					$attributes[$attribute] = $split_lookup[0];
 					if ($split_lookup[0] === 'float') {
 						$split_value = explode('.', $split_lookup[1]);

 						$attributes[$attribute] = $split_value[0];
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