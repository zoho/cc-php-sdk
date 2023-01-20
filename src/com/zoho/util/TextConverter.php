<?php
namespace com\zoho\util;

use com\zoho\Initializer;

use com\zoho\util\CommonAPIHandler;

/**
 * This class processes the API response object to the POJO object and POJO object to a JSON object.
 */
class TextConverter extends Converter
{
    private $_uniqueValuesMap = array();

    public function __construct($commonAPIHandler)
    {
        parent::__construct($commonAPIHandler);
    }

    public function appendToRequest(&$requestBase, $requestObject)
    {
//        $requestBase[CURLOPT_POSTFIELDS] = json_encode($requestObject, JSON_UNESCAPED_UNICODE);
    }

    public function formRequest($requestInstance, $pack, $instanceNumber, $memberDetail=null)
    {
        return null;
    }
   
    public function getWrappedResponse($response, $pack)
    {
        list ($headers, $content) = explode("\r\n\r\n", strval($response), 2);

        if ($content != null)
        {
            return $this->getResponse($content, $pack);
        }

        return null;
    }

    public function getResponse($responseJSON, $packageName)
    {
        $instance = null;

        if (empty($responseJSON) || $responseJSON == null)
        {
            return $instance;
        }

        $classDetail = Initializer::$jsonDetails[$packageName];
    
        $instance = new $packageName();

        foreach ($classDetail as $memberName => $keyDetail)
        {
            $reflector = new \ReflectionClass($instance);

            $member = $reflector->getProperty($memberName);

            $member->setAccessible(true);

            $member->setValue($instance, $responseJSON);
        }

        return $instance;
    }
}