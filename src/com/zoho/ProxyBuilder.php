<?php
namespace com\zoho;

use com\zoho\util\Constants;

class ProxyBuilder
{
    private $host;

    private $port;

    private $user;

    private $password = "";

    public function host(string $host)
    {
        ProxyBuilder::assertNotNull($host, Constants::REQUEST_PROXY_ERROR, Constants::HOST_ERROR_MESSAGE);

        $this->host = $host;

        return $this;
    }

    public function port(int $port)
    {
        ProxyBuilder::assertNotNull($port, Constants::REQUEST_PROXY_ERROR, Constants::PORT_ERROR_MESSAGE);

        $this->port = $port;

        return $this;
    }

    public function user(string $user)
    {
        $this->user = $user;

        return $this;
    }

    public function password(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function build()
    {
        ProxyBuilder::assertNotNull($this->host, Constants::REQUEST_PROXY_ERROR, Constants::HOST_ERROR_MESSAGE);

        ProxyBuilder::assertNotNull($this->port, Constants::REQUEST_PROXY_ERROR, Constants::PORT_ERROR_MESSAGE);

        $class = new \ReflectionClass(RequestProxy::class);

        $constructor = $class->getConstructor();

        $constructor->setAccessible(true);

        $object = $class->newInstanceWithoutConstructor();

        $constructor->invoke($object, $this->host, $this->port, $this->user, $this->password);

        return $object;
    }

    public static function assertNotNull($environment, $errorCode, $errorMessage) {
        if ($environment == null) {
            throw new SDKException($errorCode, $errorMessage);
        }
    }
}
?>