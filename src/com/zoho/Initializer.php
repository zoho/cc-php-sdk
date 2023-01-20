<?php
namespace com\zoho;

use com\zoho\api\logger\Levels;

use com\zoho\api\logger\Logger;

use com\zoho\exception\SDKException;

use com\zoho\sdkconfigbuilder\SDKConfig;

use com\zoho\util\Constants;

use com\zoho\util\RequestProxy;

use com\zoho\dc\Environment;

use com\zoho\api\logger\SDKLogger;


/**
 * This class to initialize Zoho CRM SDK.
 */
class Initializer
{
    public static $LOCAL = array();

    private static $initializer;

    private $environment = null;

    private $store = null;

    private $user = null;

    private $token = null;

    public static $jsonDetails = null;

    private $resourcePath = null;

    private $requestProxy = null;

    private $sdkConfig = null;

    /**
     * This to initialize the SDK.
     * @param Environment $environment A Environment class instance containing the CRM API base URL and Accounts URL.
     * @param SDKConfig $ A SDKConfig class instance containing the SDK configuration.
     * @param Logger $logger A Logger class instance containing the log file path and Logger type.
     * @param RequestProxy $proxy A RequestProxy class instance containing the proxy properties of the user.
     */
    public static function initialize($environment, $sdkConfig, $logger=null, $proxy=null)
    {
        try
        {
            SDKLogger::initialize($logger);

            try
            {
                if(is_null(self::$jsonDetails))
                {
                    self::$jsonDetails = json_decode(file_get_contents(explode("src/com", realpath(__DIR__))[0] . Constants::JSON_DETAILS_FILE_PATH), true);
                }
            }
            catch (\Exception $ex)
            {
                throw new SDKException(Constants::JSON_DETAILS_ERROR, null, null, $ex);
            }

            self::$initializer = new Initializer();

            $initializer = new Initializer();

            $initializer->environment = $environment;

            $initializer->sdkConfig = $sdkConfig;

            $initializer->requestProxy = $proxy;

            self::$LOCAL[$initializer->getEncodedKey($environment)] = $initializer;

            self::$initializer = $initializer;
        }
        catch(SDKException $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            throw new SDKException(Constants::INITIALIZATION_EXCEPTION, null, null, $e);
        }
    }

    public static function getJSON($filePath)
    {
        return json_decode(file_get_contents($filePath),TRUE);
    }

    /**
     * This method to get Initializer class instance.
     *
     * @return Initializer A Initializer class instance representing the SDK configuration details.
     */
    public static function getInitializer()
    {
        if (!empty(self::$LOCAL) && count(self::$LOCAL) != 0)
        {
            $initializer = new Initializer();

            $key = $initializer->getEncodedKey(self::$initializer->environment);

            if(array_key_exists($key, self::$LOCAL))
            {
                return self::$LOCAL[$key];
            }
        }

        return self::$initializer;
    }

    /**
     * This method to switch the different user in SDK environment.
     * @param Environment $environment A Environment class instance containing the CRM API base URL and Accounts URL.
     * @param SDKConfig $sdkConfig A SDKConfig class instance containing the SDK configuration.
     */
    public static function switchUser($environment, $sdkConfig, $proxy=null)
    {
        $initializer = new Initializer();

        $initializer->environment = $environment;

        $initializer->sdkConfig = $sdkConfig;

        $initializer->requestProxy = $proxy;

        self::$LOCAL[$initializer->getEncodedKey($environment)] = $initializer;

        self::$initializer = $initializer;

        SDKLogger::info(Constants::INITIALIZATION_SWITCHED . $initializer->toString());
    }

    /**
     * This is a getter method to get API environment.
     *
     * @return Environment A Environment representing the API environment.
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * This is a getter method to get API environment.
     *
     * @return TokenStore A TokenStore class instance containing the token store information.
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * This is a getter method to get CRM User.
     *
     * @return UserSignature A User class instance representing the CRM user.
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * This is a getter method to get RequestProxy.
     *
     * @return RequestProxy A RequestProxy class instance representing the proxy.
     */
    public function getRequestProxy()
    {
        return $this->requestProxy;
    }


    /**
     * This is a getter method to get OAuth client application information.
     *
     * @return Token A Token class instance representing the OAuth client application information.
     */
    public function getToken()
    {
        return $this->token;
    }

    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    /**
     * This is a getter method to get SDK configuration.
     * @return SDKConfig A SDKConfig instance representing the configuration
     */
    public function getSDKConfig()
    {
        return $this->sdkConfig;
    }

    public static function removeUserConfiguration($user, $environment)
    {
        $initializer = new Initializer();

        $key = $initializer->getEncodedKey($environment);

        if(array_key_exists($key, self::$LOCAL))
        {
            unset(self::$LOCAL[$initializer->getEncodedKey($environment)]);
        }
        else
        {
            $exception = new SDKException(null, Constants::USER_NOT_FOUND_ERROR_MESSAGE);

            SDKLogger::info(Constants::USER_NOT_FOUND_ERROR . $exception);

            throw $exception;
        }
    }

    private function getEncodedKey($environment)
    {

        $key = $environment->getUrl();

        $input = unpack('C*', utf8_encode($key));

        return base64_encode(implode(array_map("chr", $input)));
    }
}
?>