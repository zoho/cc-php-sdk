<?php
namespace com\zoho\dc;

use com\zoho\dc\DataCenter;

/**
 * This class represents the properties of Zoho CRM in AU Domain.
 */
class AUDataCenter extends DataCenter
{
    private static $PRODUCTION = null;

    private static $AU = null;

    /**
     * This Environment class instance represents the Zoho CRM Production Environment in AU Domain.
     * @return Environment A Environment class instance.
     */
    public static function PRODUCTION()
    {
        self::$AU = new AUDataCenter();

        if (self::$PRODUCTION == null)
        {
            self::$PRODUCTION = DataCenter::setEnvironment("https://crm.zohopublic.com.au", self::$AU->getIAMUrl(), self::$AU->getFileUploadUrl(), "au_prd");
        }

        return self::$PRODUCTION;
    }

    public function getIAMUrl()
    {
        return "https://accounts.zoho.com.au/oauth/v2/token";
    }

    public function getFileUploadUrl()
    {
        return "https://content.zohoapis.com.au";
    }
}