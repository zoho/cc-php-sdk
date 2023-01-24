<?php
namespace com\zoho;

use com\zoho\util\Constants;

use com\zoho\util\HeaderParamValidator;

use com\zoho\exception\SDKException;

use com\zoho\util\DataTypeConverter;

/**
 * This class representing the HTTP parameter name and value.
 */
class ParameterMap
{
    private $parameterMap = array();

    /**
     * This is a getter method to get parameter map.
     * @return array An array representing the API request parameters.
     */
    public function getParameterMap()
    {
        return $this->parameterMap;
    }

    /**
     * This is a setter method to set parameter map.
     * @param array An array representing the API request parameters.
     */
    public function setParameterMap(array $parameterMap)
    {
        $this->parameterMap = $parameterMap;
    }

    /**
     * This method to add parameter name and value.
     * @param Param $param A Param class instance.
     * @param object $value A object containing the parameter value.
     */
    public function add(Param $param, $value)
    {
        if($param === null)
        {
            throw new SDKException(Constants::PARAMETER_NULL_ERROR, Constants::PARAM_INSTANCE_NULL_ERROR);
        }

        $paramName = $param->getName();

        if($paramName === null)
        {
            throw new SDKException(Constants::PARAM_NAME_NULL_ERROR, Constants::PARAM_NAME_NULL_ERROR_MESSAGE);
        }
        
        else {
            if(preg_match('/[\'^£$%&*()}{@#~?><>,:;.|!=_+¬-]/', $paramName) || strpos($paramName, "/") !== false) {
                throw new SDKException(Constants::INVALID_PARAM, "Only Alphabets, Numbers and Underscore( _ ) are allowed for Param :: ".$paramName);
            }
        }

        if($value === null)
        {
            throw new SDKException(Constants::PARAMETER_NULL_ERROR, $paramName.Constants::NULL_VALUE_ERROR_MESSAGE);
        }else if(!($value instanceof \DateTime) && !is_string($value) && !is_bool($value) && !is_int($value) && !checkdate($value)) {
            throw new SDKException(Constants::INVALID_DATA_TYPE, $paramName.", Please use the proper datatype");
       }
       
       if(is_int($value) && strlen((string)$value) > 9) {
           throw new SDKException('Invalid data', $paramName.", value should not exceed 9 digits for Integer");
       }

        $paramClassName = $param->getClassName();

        if($paramClassName == null) {
            throw new SDKException(Constants::INVALID_CLASS_NAME, "ClassName should not be null");
        }

        $parsedParamValue = null;

        if($paramClassName != null)
        {
            $headerParamValidator = new HeaderParamValidator();

            $parsedParamValue = $headerParamValidator->validate($param, $value);
        }
        else
        {
            try
            {
                $parsedParamValue = DataTypeConverter::postConvert($value, get_class($value));
            }
            catch(\Exception $ex)
            {
                $parsedParamValue = $value;
            }
        }

        if($parsedParamValue === true || $parsedParamValue === false)
        {
            $parsedParamValue = json_encode($parsedParamValue, JSON_UNESCAPED_UNICODE);
        }

        if(strpos($parsedParamValue, "::") !== false) {
			throw new SDKException(Constants::RESERVE_KEYWORD_USAGE_ERROR, "Don't use this reserve keyword - ::");
		}

        if(!(strtolower($paramName) === "digestkey") && !(strtolower($paramName) === "processname") && !(strtolower($paramName) === "statename") && !(strtolower($paramName) === "digest") && !(strtolower($paramName) === "identifier1") && !(strtolower($paramName) === "identifier2") && !(strtolower($paramName) === "identifier3") && !(strtolower($paramName) === "identifier4") && !(strtolower($paramName) === "identifier5")) {
			$parsedParamValue = $parsedParamValue . "::" . $paramClassName;
		}

        if (array_key_exists($paramName, $this->parameterMap))
        {
            throw new SDKException(Constants::PARAMETER_DUPLICATE_ERROR, "Please Don't use duplicate params");
        }
        else
        {
            $this->parameterMap[$paramName] = $parsedParamValue;
        }
    }
}
