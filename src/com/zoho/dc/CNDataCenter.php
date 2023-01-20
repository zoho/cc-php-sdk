<?php
namespace com\zoho\dc;

use com\zoho\dc\DataCenter;

/**
 * This class represents the properties of Zoho CRM in CN Domain.
 */
class CNDataCenter extends DataCenter
{
    private static $PRODUCTION = null;

    private static $CN = null;

    /**
     * This Environment class instance represents the Zoho CRM Production Environment in CN Domain.
     * @return Environment A Environment class instance.
     */
    public static function PRODUCTION()
    {
        self::$CN = new CNDataCenter();

        if (self::$PRODUCTION == null)
        {
            self::$PRODUCTION = DataCenter::setEnvironment("https://crm.zohopublic.com.cn", self::$CN->getIAMUrl(), self::$CN->getFileUploadUrl(), "cn_prd");
        }

        return self::$PRODUCTION;
    }

    public function getIAMUrl()
    {
        return "https://accounts.zoho.com.cn/oauth/v2/token";
    }

    public function getFileUploadUrl()
    {
        return "https://content.zohoapis.com.cn";
    }
}