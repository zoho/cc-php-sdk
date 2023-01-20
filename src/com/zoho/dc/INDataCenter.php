<?php
namespace com\zoho\dc;

use com\zoho\dc\DataCenter;

/**
 * This class represents the properties of Zoho CRM in IN Domain.
 */
class INDataCenter extends DataCenter
{
    private static $PRODUCTION = null;

    private static $IN = null;

    /**
     * This Environment class instance represents the Zoho CRM Production Environment in IN Domain.
     * @return Environment A Environment class instance.
     */
    public static function PRODUCTION()
    {
        self::$IN = new INDataCenter();

        if (self::$PRODUCTION == null)
        {
            self::$PRODUCTION = DataCenter::setEnvironment("https://crm.zohopublic.in", self::$IN ->getIAMUrl(), self::$IN->getFileUploadUrl(), "in_prd");
        }

        return self::$PRODUCTION;
    }

    public function getIAMUrl()
    {
        return "https://accounts.zoho.in/oauth/v2/token";
    }

    public function getFileUploadUrl()
    {
        return "https://content.zohoapis.in";
    }
}