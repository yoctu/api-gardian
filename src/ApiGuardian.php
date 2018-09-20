<?php

namespace Yoctu\ApiGuardian;

use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Config\Param;
use Yoctu\ApiGuardian\Exception\InvalidTokenException;
use Yoctu\ApiGuardian\Exception\NoTokenProvidedException;
use Zend\Diactoros\Request;

/**
 * Class Apiguardian
 * @package Yoctu\ApiGuardian
 */
class ApiGuardian
{
    /** @var array */
    protected $optionalKeys = [];

    /**
     * If for some reason you want to add more keys to the list of authorized api keys you can do it here.
     *
     * @param array $optionalKeys
     */
    public function setOptionalKeys(array $optionalKeys)
    {
        $this->optionalKeys = $optionalKeys;
    }

    /**
     * @param ApplicationInterface $app
     *
     * @throws \Yoctu\ApiGuardian\Exception\InvalidTokenException
     * @throws  \Yoctu\ApiGuardian\Exception\NoTokenProvidedException
     * @throws \ObjectivePHP\ServicesFactory\Exception\Exception
     */
    public function __invoke(ApplicationInterface $app)
    {
        $apiKeys = array_merge($app->getConfig()->subset(Param::class)->get('api-keys'), $this->optionalKeys);

        // If your app have some form of user provider you can use an user api token to the list
        if ($app->getServicesFactory()->has('user')) {
            $apiKeys = array_merge($apiKeys, [$app->getServicesFactory()->get('user')->getApiToken()]);
        }

        $apiKeys = array_filter($apiKeys);

        // If no api keys are provided, we pass the verification
        if (empty($apiKeys)) {
            return;
        }

        /** @var Request $request */
        $request = $app->getRequest();

        // Fix for issue #1
        $apacheHeaders = [];
        if (\function_exists('apache_request_headers')) {
            $apacheHeaders = apache_request_headers();
        }

        if (!isset($apacheHeaders['authorization']) && !$request->hasHeader('Authorization')) {
            throw new NoTokenProvidedException();
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $authHeader = empty($authHeader) ? $apacheHeaders['authorization'] : $authHeader;

        if (!\in_array($authHeader, $apiKeys, true)) {
            throw new InvalidTokenException();
        }
    }
}
