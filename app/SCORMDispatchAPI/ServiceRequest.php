<?php

namespace App\SCORMDispatchAPI;

require_once 'Configuration.php';

class ServiceRequest{
	
	/**
     * Number of seconds to wait while connecting to the server.
     */
    const TIMEOUT_CONNECTION = 30;
    /**
     * Total number of seconds to wait for a request.
     */
    const TIMEOUT_TOTAL = 500;
	
	private $_configuration = null;
	private $_methodParams = array();
	private $_fileToPost = null;
    private $_dataToPost = null;

	public function __construct($configuration) {
		$this->_configuration = $configuration;
	}
	
	public function setMethodParams($paramsArray)
	{
		$this->_methodParams = array_merge($this->_methodParams, $paramsArray);
	}
	
	public function setFileToPost($fileName)
	{
		$this->_fileToPost = $fileName;
	}
	
    public function setDataToPost($dataName)
    {
        $this->_dataToPost = $dataName;
    }

	public function CallService($methodName)
	{
		$postParams = null;
		if(isset($this->_fileToPost))
		{
			//TODO - check to see if this file exists
			if (!function_exists('curl_file_create')) {
				// PHP version older than 5.5
				$postParams = array('filedata' => "@$this->_fileToPost");
			} else {
				// PHP 5.5 and higher uses curl_file_create.
				$postParams = array('filedata' => curl_file_create($this->_fileToPost));
			}
		}

        if(isset($this->_dataToPost))
        {        
            $postParams = $this->_dataToPost;
        }

		$url = $this->ConstructUrl($methodName);
        
		$responseText = $this->submitHttpPost($url,$postParams, self::TIMEOUT_TOTAL);
        $response = json_decode($responseText, true);
        return $response;
	}

    public function ConstructUrl($methodName)
    {
        return $this->ConstructAppAgnosticUrl($this->_configuration->getAppId(), 
                                        $this->_configuration->getSecurityKey(), 
                                        $methodName);
    }
	
	public function ConstructAppAgnosticUrl($appId, $secretKey, $methodName)
	{
        $sig = md5($secretKey . $appId);
        $parameterMap = array(  $methodName,
                                $appId,
                                $sig
                            );

        $params = array();
		array_merge($params, $this->_methodParams);
		foreach($this->_methodParams as $key => $value)
		{
			$params[$key] = $value;            
		}       
        
        $url = $this->_configuration->getSCORMDispatchServiceUrl();
        $p = $this->signMainParams($secretKey,$parameterMap);
        $params = $this->signParams($secretKey,$params);     

        $url .= '/' . $p;
        if ($params != '')
            $url .= '?' . $params;

		return $url;
	}
	//Y2lkPTkzJnVybD1odHRwOi8vbG9jYWxob3N0L1NDT1JNRGlzcGF0Y2hBcGkvZGVtby9HZXRDb3Vyc2VMaXN0LnBocA==
    //Y2lkPTkzJnVybD1odHRwOi8vbG9jYWxob3N0L1NDT1JNRGlzcGF0Y2hBcGkvZGVtby9HZXRDb3Vyc2VMaXN0LnBocA== 
	/**
     * Submit a POST request with to the specified URL with given parameters.
     *
     * @param   string $url
     * @param   array $params An optional array of parameter name/value
     *          pairs to include in the POST.
     * @param   integer $timeout The total number of seconds, including the
     *          wait for the initial connection, wait for a request to complete.
     * @return  string
     * @uses    TIMEOUT_CONNECTION to determine how long to wait for a
     *          for a connection.
     * @uses    TIMEOUT_TOTAL to determine how long to wait for a request
     *          to complete.
     * @uses    set_time_limit() to ensure that PHP's script timer is five
     *          seconds longer than the sum of $timeout and TIMEOUT_CONNECTION.
     */
    static function submitHttpPost($url, $postParams = null,  $timeout = self::TIMEOUT_TOTAL)
    {
        $ch = curl_init();
        //$url = 'http://localhost/capture/index.php';
        // set up the request
        
        curl_setopt($ch, CURLOPT_URL, $url);
        // make sure we submit this as a post
        curl_setopt($ch, CURLOPT_POST, 1);
        if (isset($postParams)) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        } else{
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "");        	
        }
        // make sure problems are caught
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        // return the output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // set the timeouts
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TIMEOUT_CONNECTION);
        curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        // set the PHP script's timeout to be greater than CURL's
        set_time_limit(self::TIMEOUT_CONNECTION + $timeout + 5);

        $result = curl_exec($ch);
        // check for errors
        if (0 == curl_errno($ch)) {
            curl_close($ch);
            return $result;
        } else {
            echo ('Request to '.$url.' failed. ERROR: '.curl_error($ch));
            curl_close($ch);
        }
        
    }    

    /**
     * Create a signed signature of the parameters.
     *
     * Return a parameter string that can be tacked onto the end of a URL.
     * Items will be sorted and an api_sig element will be on the end.
     */
    static function signParams($secret, $params)
    {
        $signing = '';
        $values = array();
        if (defined('SORT_FLAG_CASE')) { //PHP 5.4 and higher only.
            ksort($params, SORT_STRING | SORT_FLAG_CASE);
        } else {
            ksort($params, SORT_STRING);
        }

        foreach($params as $key => $value) {
            $values[] = $key . '=' . urlencode($value);
            //$values[] = $key . '=' . $value;
        }       
        return implode('&', $values);
    }

    static function signMainParams($secret, $params)
    {
        $signing = '';
        $values = array();
        if (defined('SORT_FLAG_CASE')) { //PHP 5.4 and higher only.
            ksort($params, SORT_STRING | SORT_FLAG_CASE);
        } else {
            ksort($params, SORT_STRING);
        }

        foreach($params as $key => $value) {            
            $values[] = $value;
        }       
        return implode('/', $values);
    }

}

?>
