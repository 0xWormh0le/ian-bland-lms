<?php

namespace App\SCORMDispatchAPI;

class Configuration{
	
	private $_appId = null;
    private $_securityKey = null;
    private $_scormServiceUrl = null;

	public function __construct($scormServiceUrl, $appId, $securityKey) {
		// scormEngineServiceUrl (required)
		if (isset($scormServiceUrl)) {
			$this->setSCORMDispatchServiceUrl($scormServiceUrl);
		} else {
			
		}
		// appId (required)
		if (isset($appId)) {
			$this->setAppId($appId);
		} else {
			
		}
		// securityKey (required)
		if (isset($securityKey)) {
			$this->setSecurityKey($securityKey);
		} else {

		}		
	}
	
	public function getAppId()
	{
		return $this->_appId;
	}
	public function setAppId($appId)
	{
		$this->_appId = $appId;
	}
	
	public function getSecurityKey()
	{
		return $this->_securityKey;
	}
	public function setSecurityKey($securityKey)
	{
		$this->_securityKey = $securityKey;
	}	
	public function getSCORMDispatchServiceUrl()
	{
		return $this->_scormServiceUrl;
	}
	public function setSCORMDispatchServiceUrl($scormServiceUrl)
	{
		$this->_scormServiceUrl = $scormServiceUrl;
	}
	
}
?>
