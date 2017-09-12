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

 	/**
 	 * [getLookupFromArray description]
 	 * @param  [type] $data   [description]
 	 * @param  [type] $column [description]
 	 * @param  [type] $value  [description]
 	 * @return false | string
 	 */
 	public static function getLookupFromArray($data, $column, $value){
 		$key = array_search($value, array_column($data, $column));

 		return $key? $data[$key]: $key;
 	}

 	public static function checkResults($results){
 		return isset($results['warning'])? []: $results;
 	}

 	// Create LOV
	public static function createLOV($list, $key, $value){
 		$lov = [];
 		foreach ($list as $item) {
 			$lov[$item[$key]] = $item[$value];
 		}

 		return $lov;
 	}

	// Create an access token for API
	public static function createAccessToken($length){
 		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
 	}

 	// Parsing request parameter
 	public static function extractRequestParam($request){
 		return $request->isGet()? $request->getQueryParams(): $request->getParsedBody();
 	}

 	// Validate an email
 	public static function validateEmail($email){
 		return !(filter_var($email, FILTER_VALIDATE_EMAIL) === false);
 	}

 	// Check required parameters before process to database
 	public static function checkRequiredParam($param, $required_param){
 		foreach ($required_param as $key => $type) {
 			if (isset($param[$key])) {
 				if ($type == REQ_TYPE_NUMERIC && is_numeric($param[$key])) {
 					continue;
 				} else if ($type == REQ_TYPE_EMAIL && Helpers::validateEmail($param[$key])) {
 					continue;
 				} else if ($type == REQ_TYPE_TEXT) {
 					continue;
 				} else {
 					return false;
 				}
 			} else {
 				return false;
 			}
 		}

 		return true;
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

	public static function getRouteGroup ($uri) {
		$uri = split("/", $uri);
		return $uri[0];
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