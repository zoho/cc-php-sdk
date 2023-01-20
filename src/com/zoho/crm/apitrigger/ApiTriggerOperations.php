<?php 
namespace com\zoho\crm\apitrigger;

use com\zoho\ParameterMap;
use com\zoho\crm\Param;
use com\zoho\exception\SDKException;
use com\zoho\util\CommonAPIHandler;
use com\zoho\util\Constants;
use com\zoho\util\APIResponse;

class ApiTriggerOperations
{
	/**
	 * The method to get api trigger with param
	 * @param ParameterMap $paramInstance An instance of ParameterMap
	 * @return APIResponse An instance of APIResponse
	 */
	public  function getAPITriggerWithParam(ParameterMap $paramInstance=null)
	{
		if($paramInstance != null && !($paramInstance instanceof ParameterMap))	{
			throw new SDKException(Constants::TYPE_ERROR, "KEY: paramInstance EXPECTED TYPE: ParameterMap");
		}

		$keyword = null;
		$processNameCount = $stateNameCount = $zgIdCount = $identifier1Count = $identifier2Count = $identifier3Count = $identifier4Count = $identifier5Count = $otherParamCount = 0;
		$isLengthExceeded = $isDigestUsed = false;
		foreach($paramInstance->getParameterMap() as $key => $value) {
			$keyword = strtolower($key);
			switch($keyword) {
			case "processname":
				$processNameCount = $processNameCount + 1;
				break;
			case "statename":
				$stateNameCount = $stateNameCount + 1;
				break;
			case "digestkey":
				$zgIdCount = $zgIdCount + 1;
				break;
			case "identifier1":
				$identifier1Count = $identifier1Count + 1;
				break;
			case "identifier2":
				$identifier2Count = $identifier2Count + 1;
				break;
			case "identifier3":
				$identifier3Count = $identifier3Count + 1;
				break;
			case "identifier4":
				$identifier4Count = $identifier4Count + 1;
				break;
			case "identifier5":
				$identifier5Count = $identifier5Count + 1;
				break;
			case "digest":
				$isDigestUsed = true;
				break;
			default:
				$otherParamCount = $otherParamCount + 1;
				break;
			}
			if(strlen($keyword) >= 255 || strlen($value) >= 255) {
				$isLengthExceeded = true;
			}
		}
		if($processNameCount == 0 || $stateNameCount == 0 || $zgIdCount == 0) {
			throw new SDKException(Constants::MANDATORY_NOT_FOUND, Constants.MANDATORY_KEY_ERROR);
		}else if ($identifier1Count == 0 && $identifier2Count == 0 && $identifier3Count == 0 && $identifier4Count == 0 && $identifier5Count == 0) {
			throw new SDKException(Constants::MANDATORY_NOT_FOUND, "Please give atleast one identifier");
		}else if($processNameCount >= 2 || $stateNameCount >= 2 || $zgIdCount >= 2 || $identifier1Count >= 2 || $identifier2Count >= 2 || $identifier3Count >= 2 || $identifier4Count >= 2 || $identifier5Count >= 2) {
			throw new SDKException(Constants::PARAMETER_DUPLICATE_ERROR, "Please Don't use duplicate params");
		}else if ($otherParamCount > 2) {
			throw new SDKException(Constants::PARAM_LIMIT_EXCEED_ERROR, "Params limit exceeded. Please don't give more than 2 params");
		}else if($isDigestUsed) {
			throw new SDKException(Constants::RESERVE_KEYWORD_USAGE_ERROR, "Please don't use the reserve keyword digest");
		}else if($isLengthExceeded) {
			throw new SDKException(Constants::PARAM_LENGTH_EXCEED_ERROR, "The param length should not exceed the size 255");
		}

		$handlerInstance=new CommonAPIHandler(); 
		$apiPath=""; 
		$apiPath=$apiPath.('/commandcenter'); 
		$handlerInstance->setAPIPath($apiPath); 
		$handlerInstance->setHttpMethod(Constants::REQUEST_METHOD_GET); 
		$handlerInstance->setCategoryMethod(Constants::REQUEST_CATEGORY_READ); 
		$handlerInstance->setParam($paramInstance); 
		return $handlerInstance->apiCall(APIGET::class, 'text/plain'); 

	}
} 
