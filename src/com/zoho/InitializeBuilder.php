<?php

namespace com\zoho;

use com\zoho\util\Constants;

use com\zoho\exception\SDKException;

use com\zoho\sdkconfigbuilder\SDKConfig;

use com\zoho\dc\Environment;

use com\zoho\api\logger\LogBuilder;

use com\zoho\RequestProxy;

use com\zoho\api\logger\Levels;

use com\zoho\api\logger\Logger;

class InitializeBuilder
{
    private $environment;

    private $requestProxy;

    private $sdkConfig;

    private $logger;

    private $errorMessage;

    private $initializer;

    function __construct()
    {
        $this->initializer = Initializer::getInitializer();

        $this->errorMessage = (Initializer::getInitializer() != null) ? Constants::SWITCH_USER_ERROR : Constants::INITIALIZATION_ERROR;

        if(Initializer::getInitializer() != null)
        {

            $this->environment = Initializer::getInitializer()->getEnvironment();

            $this->sdkConfig = Initializer::getInitializer()->getSDKConfig();
        }
    }

    public function initialize()
    {

        InitializeBuilder::assertNotNull($this->environment, $this->errorMessage, Constants::ENVIRONMENT_ERROR_MESSAGE);

        if(is_null($this->sdkConfig))
        {
            $this->sdkConfig = (new SDKConfigBuilder())->build();
        }

        if(is_null($this->logger))
        {
            $this->logger = (new LogBuilder())->level(Levels::INFO)->filePath(getcwd() . DIRECTORY_SEPARATOR . Constants::LOG_FILE_NAME)->build();
        }

        Initializer::initialize($this->environment, $this->sdkConfig, $this->logger, $this->requestProxy);
    }

    public function switchUser()
    {
        InitializeBuilder::assertNotNull($this->environment, $this->errorMessage, Constants::ENVIRONMENT_ERROR_MESSAGE);

        Initializer::switchUser($this->environment, $this->sdkConfig, $this->requestProxy);
    }

    public function logger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function SDKConfig(SDKConfig $sdkConfig)
    {
        $this->sdkConfig = $sdkConfig;

        return $this;
    }

    public function requestProxy(RequestProxy $requestProxy)
    {
        $this->requestProxy = $requestProxy;

        return $this;
    }

    public function environment(Environment $environment)
    {
        InitializeBuilder::assertNotNull($environment, $this->errorMessage, Constants::ENVIRONMENT_ERROR_MESSAGE);

        $this->environment = $environment;

        return $this;
    }

    public static function assertNotNull($environment, $errorCode, $errorMessage) {
        if ($environment == null) {
            throw new SDKException($errorCode, $errorMessage);
        }
    }
}
?>