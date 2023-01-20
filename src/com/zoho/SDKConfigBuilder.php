<?php
namespace com\zoho;

/**
 * The Builder class to build SDKConfig
 */
class SDKConfigBuilder
{
    private $enableSSLVerification;

    private $connectionTimeout;

    private $timeout;

    public function __Construct()
    {

        $this->enableSSLVerification = true;

        $this->connectionTimeout = 0;

        $this->timeout = 0;
    }

    /**
     * This is a setter method to set enableSSLVerification.
     * @param enableSSLVerification
     */
    public function sslVerification(bool $enableSSLVerification)
    {
        $this->enableSSLVerification = $enableSSLVerification;

        return $this;
    }

    /**
     * This is a setter method to set connectionTimeout.
     * @param connectionTimeout A int number of seconds to wait while trying to connect.
     * @return An instance of Builder
     */
    public function connectionTimeout(int $connectionTimeout)
    {
        $this->connectionTimeout = $connectionTimeout > 0 ? $connectionTimeout : 0;

        return $this;
    }

    /**
     * This is a setter method to set timeout.
     * @param timeout A int maximum number of seconds to allow cURL functions to execute.
     * @return An instance of Builder
     */
    public function timeout(int $timeout)
    {
        $this->timeout = $timeout > 0 ? $timeout : 0;

        return $this;
    }

    // CURLOPT_CONNECTTIMEOUT is a segment of the time represented by CURLOPT_TIMEOUT, so the value of the CURLOPT_TIMEOUT should be greater than the value of the CURLOPT_CONNECTTIMEOUT.

    /**
     * The method to build the SDKConfig instance
     * @returns An instance of SDKConfig
     */
    public function build()
    {
        return new \com\zoho\sdkconfigbuilder\SDKConfig($this->enableSSLVerification, $this->connectionTimeout, $this->timeout);
    }
}

namespace com\zoho\sdkconfigbuilder;

/**
 * The class to configure the SDK.
 */
class SDKConfig
{
    private $autoRefreshFields;

    private $pickListValidation;

    private $enableSSLVerification;

    private $connectionTimeout;

    private $timeout;

    /**
     * Creates an instance of SDKConfig with the given parameters
     * @param autoRefreshFields - A boolean representing autoRefreshFields
     * @param pickListValidation - A boolean representing pickListValidation
     * @param enableSSLVerification - A boolean representing enableSSLVerification
     */
    public function __Construct(bool $enableSSLVerification, int $connectionTimeout, int $timeout)
    {
        $this->enableSSLVerification = $enableSSLVerification;

        $this->connectionTimeout = $connectionTimeout;

        $this->timeout = $timeout;
    }

    /**
     * This is a getter method to get enableSSLVerification.
     * @return A boolean representing enableSSLVerification
     */
    public function isSSLVerificationEnabled()
    {
        return $this->enableSSLVerification;
    }

    /**
	 * This is a getter method to get connectionTimeout.
	 * @return A int representing connectionTimeout
	 */
	public function connectionTimeout()
	{
		return $this->connectionTimeout;
	}

	/**
	 * This is a getter method to get cURL Timeout.
	 * @return A int representing cURL Timeout
	 */
	public function timeout()
	{
		return $this->timeout;
	}
}
?>