<?php
namespace com\zoho\dc;

use com\zoho\dc\DataCenter;

/***
 * This class represents the properties of Zoho CRM in EU Domain.
 */
class EUDataCenter extends DataCenter
{
    private static $PRODUCTION = null;

    private static $EU = null;

    /**
     * This Environment class instance represents the Zoho CRM Production Environment in EU Domain.
     * @return Environment A Environment class instance.
     */
    public static function PRODUCTION()
    {
        self::$EU = new EUDataCenter();

        if (self::$PRODUCTION == null)
        {
            self::$PRODUCTION = DataCenter::setEnvironment("https://crm.zohopublic.eu", self::$EU->getIAMUrl(), self::$EU->getFileUploadUrl(), "eu_prd");
        }

        return self::$PRODUCTION;
    }

    public function getIAMUrl()
    {
        return "https://accounts.zoho.eu/oauth/v2/token";
    }

    public function getFileUploadUrl()
    {
        return "https://content.zohoapis.eu";
    }
}