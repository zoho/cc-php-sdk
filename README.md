License
=======

    Copyright (c) 2021, ZOHO CORPORATION PRIVATE LIMITED 
    All rights reserved. 

    Licensed under the Apache License, Version 2.0 (the "License"); 
    you may not use this file except in compliance with the License. 
    You may obtain a copy of the License at 
    
        http://www.apache.org/licenses/LICENSE-2.0 
    
    Unless required by applicable law or agreed to in writing, software 
    distributed under the License is distributed on an "AS IS" BASIS, 
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
    See the License for the specific language governing permissions and 
    limitations under the License.

# ZOHOCRM PATHFINDER PHP SDK 1.0

## Table Of Contents

* [Overview](#overview)
* [Environmental Setup](#environmental-setup)
* [Including the SDK in your project](#including-the-sdk-in-your-project)
* [Configuration](#configuration)
* [Initialization](#initializing-the-application)
* [Responses And Exceptions](#responses-and-exceptions)
* [Sample Code](#sdk-sample-code)

## Overview

Zoho CRM PHP SDK offers a way to create client PHP applications that can be integrated with Zoho CRM PathFinder.

## Environmental Setup

PHP SDK is installable through **Composer**. **Composer** is a tool for dependency management in PHP. SDK expects the following from the client app.

- Client app must have PHP(version 7 and above) with curl extension enabled.

- PHP SDK must be installed into client app though **Composer**.

## Including the SDK in your project

You can include the SDK to your project using:

- Install **Composer** (if not installed).

  - Run this command to install the composer.

    ```sh
    curl -sS https://getcomposer.org/installer | php
    ```

  - To install composer on mac/linux machine:

    ```sh
    https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
    ```

  - To install composer on windows machine:

    ```sh
    https://getcomposer.org/doc/00-intro.md#installation-windows
    ```

- Install **PHP SDK**.

  - Navigate to the workspace of your client app.

  - Run the command below:

    ```sh
    composer require cc/cc-php-sdk
    ```

  - The PHP SDK will be installed and a package named vendor will be created in the workspace of your client app.

- Using the SDK.

  - Add the below line in PHP files of your client app, where you would like to make use of PHP SDK.

    ```php
    require 'vendor/autoload.php';
    ```

  Through this line, you can access all the functionalities of the PHP SDK. The namespaces of the class to be used must be included within the "use" statement.

## Configuration

Before you get started with creating your PHP application, you need to create a SDK configuration in ZOHOCRM PathFinder.

----

- Configure the API environment which decides the domain and the URL to make API calls.

    ```php
    /*
    * Configure the environment
    * which is of the pattern Domain::Environment
    * Available Domains: USDataCenter, EUDataCenter, INDataCenter, CNDataCenter, AUDataCenter
    * Available Environments: PRODUCTION()
    */
    $environment = USDataCenter::PRODUCTION();
    ```

- Create an instance of SDKConfig containing SDK configurations.

    ```php
    /*
    * By default, the SDK creates the SDKConfig instance
    */

    $sdkConfig = (new SDKConfigBuilder())->build();
    ```

## Initializing the Application

Initialize the SDK using the following code.

```php
use com\zoho\crm\api\InitializeBuilder;

use com\zoho\crm\api\dc\USDataCenter;

use com\zoho\crm\api\SDKConfigBuilder;

class PFSDKInitialize
{
    public static function initialize()
    {
   /*
    * Configure the environment
    * which is of the pattern Domain::Environment
    * Available Domains: USDataCenter, EUDataCenter, INDataCenter, CNDataCenter, AUDataCenter
    * Available Environments: PRODUCTION()
    */
    $environment = USDataCenter::PRODUCTION();

    $sdkConfig = (new SDKConfigBuilder())->build();

   /*
    * Set the following in InitializeBuilder
    * environment -> Environment instance
    * SDKConfig -> SDKConfig instance
    */
    (new InitializeBuilder())
            ->environment($environment)
            ->SDKConfig($sdkConfig)
            ->initialize();
    }
}

PFSDKInitialize::initialize();
```

- You can now access the functionalities of the SDK. Refer to the sample codes to make various API calls through the SDK.


## Responses and Exceptions

All SDK method calls return an instance of the **APIResponse** class.

Use the **getObject()** method in the returned **APIResponse** object to obtain the response handler interface (**GET**).

All other exceptions such as SDK anomalies and other unexpected behaviours are thrown under the **SDKException** class.

## SDK Sample code

```php
<?php
use com\zoho\dc\USDataCenter;
use com\zoho\InitializeBuilder;
use com\zoho\SDKConfigBuilder;
use com\zoho\crm\apitrigger\GetAPITriggerParam;
use com\zoho\crm\apitrigger\ApiTriggerOperations;
use com\zoho\Param;

use com\zoho\ParameterMap;

require_once "vendor/autoload.php";

class PFSDKInitialize
{
    public static function initialize()
    {
       /*
        * Configure the environment
        * which is of the pattern Domain::Environment
        * Available Domains: USDataCenter, EUDataCenter, INDataCenter, CNDataCenter, AUDataCenter
        * Available Environments: PRODUCTION()
        */
        $environment = USDataCenter::PRODUCTION();
        $sdkConfig = (new SDKConfigBuilder())->build();

        (new InitializeBuilder())
            ->environment($environment)
            ->SDKConfig($sdkConfig)
            ->initialize();
    
        try {
            //Pass Processname, Statename, Digestkey configured in the CRM PathFinder and pass dynamic Identifiers and Params to that PathFinder Process
            $paramInstance = new ParameterMap();
            $paramInstance->add(GetAPITriggerParam::PROCESSNAME(), "sdkprocess");
            $paramInstance->add(GetAPITriggerParam::STATENAME(), "state1");
            $paramInstance->add(GetAPITriggerParam::ZGID(), "15542307");
            $paramInstance->add(GetAPITriggerParam::IDENTIFIER3(), "test1");
            $paramInstance->add(GetAPITriggerParam::IDENTIFIER4(), "test2");
            $paramInstance->add(GetAPITriggerParam::IDENTIFIER5(), "test3");
            //Supported dataTypes for Param: String, Integer, Boolean, DateTime, Date
            $paramInstance->add(new Param("stringparam!", "string"), "xyz");
            $paramInstance->add(new Param("integerparam", "Integer"), 10);
            // $paramInstance->add(new Param("booleanparam", "Boolean"), false);
            // $paramInstance->add(new Param("datetimeparam", "DateTime"), date(DATE_ATOM, mktime(13, 30, 20, 8, 11, 2022)));
            // $paramInstance->add(new Param("dateparam", "Date"), date_create("2022-11-10"));
            
            $primaryOperations = new ApiTriggerOperations();
            //Checks the response of an API
            $response = $primaryOperations->getAPITriggerWithParam($paramInstance);

            print_r($response->getObject()->getMessage());

        }catch(\Exception $exception) {
            print_r($exception);
        }
        
        echo("\n");
    }
}
PFSDKInitialize::initialize();
?>
```