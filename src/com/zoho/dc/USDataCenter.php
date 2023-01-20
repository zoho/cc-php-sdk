<?php
namespace com\zoho\dc;

use com\zoho\dc\DataCenter;

/**
 * This class represents the properties of Zoho CRM in US Domain.
 */
class USDataCenter extends DataCenter
{
    private static $PRODUCTION = null;

    private static $US = null;

    /**
     * This Environment class instance represents the Zoho CRM Production Environment in US Domain.
     * @return Environment A Environment class instance.
     */
    public static function PRODUCTION()
    {
        self::$US = new USDataCenter();

        if (self::$PRODUCTION == null)
        {
            self::$PRODUCTION = DataCenter::setEnvironment("https://crm.zohopublic.com", self::$US->getIAMUrl(), self::$US->getFileUploadUrl(), "us_prd");
        }

        return self::$PRODUCTION;
    }

    public function getIAMUrl()
    {
        return "https://accounts.zoho.com/oauth/v2/token";
    }

    public function getFileUploadUrl()
    {
        return "https://content.zohoapis.com";
    }
}