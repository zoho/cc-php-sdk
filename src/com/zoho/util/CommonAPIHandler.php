<?php
namespace com\zoho\util;

use com\zoho\HeaderMap;

use com\zoho\Initializer;

use com\zoho\ParameterMap;

use com\zoho\Header;

use com\zoho\Param;

use Exception;

use com\zoho\api\logger\SDKLogger;

use com\zoho\exception\SDKException;

use com\zoho\util\APIHTTPConnector;

use com\zoho\util\Constants;

use com\zoho\crm\apitrigger\APIGET;

use com\zoho\util\TextConverter;

use com\zoho\util\APIResponse;

/**
 * This class is to process the API request and its response.
 * Construct the objects that are to be sent as parameters or in the request body with the API.
 * The Request parameter, header and body objects are constructed here.
 * Process the response JSON and converts it to relevant objects in the library.
 */
class CommonAPIHandler
{
    private $apiPath;

    private $param;

    private $header;

    private $request;

    private $httpMethod;

    private $moduleAPIName;

    private $contentType;

    private $categoryMethod;

	private $mandatoryChecker;

    public function __construct()
    {
        $this->header = new HeaderMap();

        $this->param = new ParameterMap();
    }

    /**
     * This is a setter method to set an API request content type.
     * @param string $contentType A string containing the API request content type.
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * This is a setter method to set the API request URL.
     * @param string $apiPath A string containing the API request URL.
     */
    public function setAPIPath($apiPath)
    {
        $this->apiPath = $apiPath;
    }

    /**
     * This method is to add an API request parameter.
     * @param string $param A Param containing the API request parameter .
     * @param object $paramValue A object containing the API request parameter value.
     */
    public function addParam($paramInstane, $paramValue)
    {
        if ($paramValue === null)
        {
            return;
        }

        if ($this->param === null)
        {
            $this->param = new ParameterMap();
        }

        $this->param->add($paramInstane, $paramValue);
    }

    /**
     * This method to add an API request header.
     * @param string $header A Header containing the API request header .
     * @param string $headerValue A object containing the API request header value.
     */
    public function addHeader($headerInstane, $headerValue)
    {
        if ($headerValue === null)
        {
            return;
        }

        if ($this->header === null)
        {
            $this->header = new HeaderMap();
        }

        $this->header->add($headerInstane, $headerValue);
    }

    /**
     * This is a setter method to set the API request parameter map.
     * @param ParameterMap $param A ParameterMap class instance containing the API request parameter.
     */
    public function setParam($param)
    {
        if ($param === null)
        {
            return;
        }

        if($this->param->getParameterMap() !== null && count($this->param->getParameterMap()) > 0)
        {
            $this->param->setParameterMap(array_merge($this->param->getParameterMap(), $param->getParameterMap()));
        }
        else
        {
            $this->param = $param;
        }
    }

    /**
     * This is a getter method to get the Zoho CRM module API name.
     * @return string A String representing the Zoho CRM module API name.
     */
    public function getModuleAPIName()
    {
        return $this->moduleAPIName;
    }

    /**
     * This is a setter method to set the Zoho CRM module API name.
     * @param string $moduleAPIName A string containing the Zoho CRM module API name.
     */
    public function setModuleAPIName($moduleAPIName)
    {
        $this->moduleAPIName = $moduleAPIName;
    }

    /**
     * This is a setter method to set the API request header map.
     * @param HeaderMap $header A HeaderMap class instance containing the API request header.
     */
    public function setHeader($header)
    {
        if ($header === null)
        {
            return;
        }

        if($this->header->getHeaderMap() !== null && count($this->header->getHeaderMap()) > 0)
        {
            $this->header->setHeaderMap(array_merge($this->header->getHeaderMap(), $header->getHeaderMap()));
        }
        else
        {
            $this->header = $header;
        }
    }

    /**
     * This is a setter method to set the API request body object.
     * @param object $request A object containing the API request body object.
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * This is a setter method to set the HTTP API request method.
     * @param string $httpMethod A string containing the HTTP API request method.
     */
    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }

    /**
     * This method is used in constructing API request and response details. To make the Zoho CRM API calls.
     * @param string $className A string containing the method return type.
     * @param string $encodeType A String containing the expected API response content type.
     * @return \com\zoho\util\APIResponse A APIResponse representing the Zoho CRM API response instance or null.
     */
    public function apiCall($className, $encodeType)
    {
        if(Initializer::getInitializer() === null)
        {
            throw new SDKException(Constants::SDK_UNINITIALIZATION_ERROR,Constants::SDK_UNINITIALIZATION_MESSAGE);
        }

        $connector = new APIHTTPConnector();

        try
        {
            $this->setAPIUrl($connector);
        }
        catch(SDKException $e)
        {
            SDKLogger::severeError(Constants::SET_API_URL_EXCEPTION, $e);

            throw $e;
        }
        catch (\Exception $e)
        {
            $exception = new SDKException(null, null, null, $e);

            SDKLogger::severeError(Constants::SET_API_URL_EXCEPTION, $exception);

            throw $exception;
        }

        $connector->setRequestMethod($this->httpMethod);

        $connector->setContentType($this->contentType);

        if ($this->header != null && count($this->header->getHeaderMap()) > 0)
        {
            $connector->setHeaders($this->header->getHeaderMap());
        }

        if ($this->param != null && count($this->param->getParameterMap()) > 0)
        {
            $connector->setParams($this->param->getParameterMap());
        }

        try
        {
            $jsonDetails = json_decode(file_get_contents(explode("src/com", realpath(__DIR__))[0] . Constants::JSON_DETAILS_FILE_PATH), true);

            $limitDetail = $this->getKeyJSONDetails("limit_handler", $jsonDetails["com\\zoho\\crm\\apitrigger\\GetAPITriggerParam"]);

            $isLimitExceeded = $limitDetail["is_limit_exahusted"];

            if(!$isLimitExceeded || !$limitDetail["api_enable_time"] || $limitDetail["api_enable_time"] <= (time() * 1000)) {
                $connector->addHeader(Constants::ZOHO_SDK, php_uname('s') . "/" . php_uname('r') . "/" . "cc/cc-php-sdk/" . phpversion() . ":" . Constants::SDK_VERSION);

                $convertInstance = new TextConverter($this);

                $response = $connector->fireRequest($convertInstance);

                $statusCode = $response[Constants::HTTP_CODE];

                $headerMap = $response[Constants::HEADERS];

                $isModel = false;

                $returnObject  = null;

                $returnObject = $convertInstance->getWrappedResponse($response[Constants::RESPONSE], $className);

                if ($returnObject !== null)
                {
                    $isModel = true;
                    $message = $returnObject->getMessage();
                    if ($message != null) {
                        $isJSONUpdated = false;
                        if ($message === "success" && $isLimitExceeded) {
                            $limitDetail["is_limit_exahusted"] = false;
                            $isJSONUpdated = true;
                        } else if (strpos($message, "The allowed limit for number of api calls per day is reached") !== false || strpos($message, "The allowed limit for PathFinder execution per day is reached") !== false) {
                            $datetime = new \DateTime();
                            $datetime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                            $datetime->setTime(23, 59, 59, 10);
                            $endOfDayinMilliSec = strtotime($datetime->format('Y-m-d\TH:i:s.vP')) * 1000;
                            $limitDetail["api_enable_time"] = $endOfDayinMilliSec;
                            $limitDetail["is_limit_exahusted"] = true;
                            $isJSONUpdated = true;
                        }
                        if ($isJSONUpdated) {
                            $jsonDetails["com\\zoho\\crm\\apitrigger\\GetAPITriggerParam"]["limit_handler"] = $limitDetail;
                            file_put_contents(explode("src/com", realpath(__DIR__))[0] . Constants::JSON_DETAILS_FILE_PATH, json_encode($jsonDetails));
                        }
                    }
                }
                return new APIResponse($headerMap, $statusCode, $returnObject, $isModel);
            }
            $headerMap = new HeaderMap();
            $apiGet = new APIGET();
            $apiGet->setMessage("The allowed limit for number of api calls per day is reached");
            return new APIResponse($headerMap, 200, $apiGet, true);
        } 
        catch (SDKException $e)
		{
            SDKLogger::severeError(Constants::API_CALL_EXCEPTION , $e);

		    throw $e;
        }
        catch (\Exception $e)
        {
            $exception = new SDKException(null, null, null, $e);

            SDKLogger::severeError(Constants::API_CALL_EXCEPTION, $exception);

            throw $exception;
        }

        return null;
    }

    public function getKeyJSONDetails($name, $json_Details)
    {
        foreach($json_Details as $json_Detail)
        {
            if(array_key_exists(Constants::NAME, $json_Detail) && strtolower($name) == strtolower($json_Detail[Constants::NAME]))
            {
                return $json_Detail;
            }
        }
    }

    /**
     * This method is used to get a Converter class instance.
     * @param string $encodeType A string containing the API response content type.
     * @return NULL|\com\zoho\util\Converter A Converter class instance.
     */
    public function getConverterClassInstance($encodeType)
    {
        switch ($encodeType)
        {
            case "application/json":
            case "text/plain":
            case "application/ld+json":
                return new JSONConverter($this);
            case "application/xml":
            case "text/xml":
                return new XMLConverter($this);
            case "multipart/form-data":
                return new FormDataConverter($this);
            case "image/png":
            case "image/jpeg":
            case "image/gif":
            case "image/tiff":
            case "image/svg+xml":
            case "image/bmp":
            case "image/webp":
            case "text/csv":
            case "text/html":
            case "text/css":
            case "text/javascript":
            case "text/calendar":
            case "application/x-download":
            case "application/zip":
            case "application/pdf":
            case "application/java-archive":
            case "application/javascript":
            case "application/octet-stream":
            case "application/xhtml+xml":
            case "application/x-bzip":
            case "application/msword":
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
            case "application/gzip":
            case "application/x-httpd-php":
            case "application/vnd.ms-powerpoint":
            case "application/vnd.rar":
            case "application/x-sh":
            case "application/x-tar":
            case "application/vnd.ms-excel":
            case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
            case "application/x-7z-compressed":
            case "audio/mpeg":
            case "audio/x-ms-wma":
            case "audio/vnd.rn-realaudio":
            case "audio/x-wav":
            case "audio/3gpp":
            case "audio/3gpp2":
            case "video/mpeg":
            case "video/mp4":
            case "video/webm":
            case "video/3gpp":
            case "video/3gpp2":
            case "font/ttf":
                return new Downloader($this);
            default:
                return null;
        }
    }

    private function setAPIUrl(APIHTTPConnector $connector)
    {
        $APIPath = "";

        if(strpos($this->apiPath, Constants::HTTP) !== false)
        {
            if(strpos($this->apiPath, Constants::CONTENT_API_URL) != false)
            {
                $APIPath = $APIPath . (Initializer::getInitializer()->getEnvironment()->getFileUploadUrl());

                try
                {
                    $uri = parse_url($this->apiPath);

                    $APIPath = $APIPath . ($uri['path']);
                }
                catch (\Exception $ex)
                {
                    $excp = new SDKException(null, null, null, $ex);

                    SDKLogger::severeError(Constants::INVALID_URL_ERROR, $excp);

                    throw $excp;
                }
            }
            else
            {
                if(substr($this->apiPath, 0, 1) == "/")
                {
                    $this->apiPath = substr($this->apiPath, 1);
                }

                $APIPath = $APIPath . ($this->apiPath);
            }
        }
        else
        {
            $APIPath = $APIPath . (Initializer::getInitializer()->getEnvironment()->getUrl());

            $APIPath = $APIPath . ($this->apiPath);
        }

        $connector->setURL($APIPath);
    }

    public function isMandatoryChecker()
	{
		return $this->mandatoryChecker;
	}

	public function setMandatoryChecker($mandatoryChecker)
	{
		$this->mandatoryChecker = $mandatoryChecker;
	}

	public function getHttpMethod()
	{
		return $this->httpMethod;
	}

	public function getCategoryMethod()
	{
		return $this->categoryMethod;
	}

	public function setCategoryMethod($category)
	{
		$this->categoryMethod = $category;
    }

    public function getAPIPath()
	{
		return $this->apiPath;
	}
}
?>
