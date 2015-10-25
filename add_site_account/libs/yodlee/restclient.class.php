<?php
namespace Yodlee;

class restClient{

	private $url_base = "";
	private $logger = "";

	// Parse the response Errors
	private static function _getErrors($errors){
		$Errors = array();
		foreach ($errors->Error as $key => $value) {
			$Errors []= $value->errorDetail;
		}
		return $Errors; 
	}

	// This function return the response 
	// in an array with key: Body.
	public function Post( $short_url, $parameters = array() ) {
		$return_values = array();

		// Create a curl handle to a non-existing location
		$ch = curl_init();

		// Avoid verification of certificate
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Concat the Url Base with the Resource Path of the Api Service required.
		$url = $this->url_base.$short_url;

		// Execute
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 360);


		if(count($parameters)>0){
			curl_setopt($ch, CURLOPT_POST, count($parameters));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
		}

		// Generate a unique ID
		$log_id = uniqid();


		$log_detail = array(
			"short_url" => $short_url,
			"long_url" => $url,
			"request" => array(
				"timer" => date("d-m-Y h:i:s"),
				"body" =>  json_encode($parameters)
				),
			"response" => array( "timer" => "", "body" =>  "" )
		);

		// Adding the log in a variable of session with its request.
		$this->logger->addLog($log_id, $log_detail);

		// Execute
		$response = curl_exec($ch);

		// Check if any error occurred
		if (curl_errno($ch)) {
               $return_values['Error'] = "Failed to reach $url.";
        } else {
			if ($response) {

				// Sometime the response could be of type: string, array, object or xml.
				// The following conditionals "If" verifies this type 
				// for assign correctly in array with Key: "Body"
				if(gettype($response) == "string"){
					$result = json_decode($response);
					if(gettype($result) == "object" || gettype($result) == "array"){
						if($result){
							$exitsError = array_key_exists("Error", $result);
							if($exitsError){
								$return_values["Body"] = array("error" => self::_getErrors($result));
							}else{
								$return_values["Body"] = $result;
							}
						}else{
							$result = ((is_array($result) && count($result)==0) || $result=="" ) ? "" : simplexml_load_string($response);
							$return_values["Body"] = $response;
						}
					}else{
						$return_values["Body"]=$response;
					}
	        	} else {
	        		$result = json_decode($response);
	        		if($result === null) {
						$return_values['Body'] = "The request does not return any value.";
					} else {
						$return_values["Body"] = $result;
					}
	        	}
			}else{
				$return_values['Body'] = "Failed to reach $url.";
			}
        }

        // Close handle
		curl_close($ch);

		if(!array_key_exists("Body", $return_values)) {
			$return_values["Body"] = $return_values;
		}

		// Adding the log in a variable of session with its request.
		$log_detail["response"] = array( "timer" => date("d-m-Y h:i:s"), "body" =>  json_encode($return_values["Body"]) );
		$this->logger->addLog($log_id, $log_detail);


		// Return a array with key: Body that contain the response of the Api Service.
		return $return_values;
	}

	// Setters
	public function setUrlBase($url_base){
		$this->url_base = $url_base;
	}

	public function setLogger(ApiLogger $logger){
		$this->logger = $logger;
	}

	// Getters
	public function getUrlBase($url_base){
		return $this->url_base;
	}

	public function getLogger(){
		return $this->logger;
	}
}