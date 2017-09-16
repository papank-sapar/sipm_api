<?php
namespace App;

use Monolog\Logger;

class Helpers {



	// Check results
	// if no data, return []
	public static function createResults($results, $select){
 		if (count($results)) {
	        $data = [];

	        foreach ($results as $item) {
	            $attributes = [];

	            foreach ($select as $field => $attribute) {
	                $split_lookup = explode(';#', isset($item[$field])? $item[$field]: '');

	                $attributes[$attribute] = $split_lookup[0];
	            }

	            $data[] = $attributes;
	        }

	        $results = $data;
	    }

        return $results;
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

 }