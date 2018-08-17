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
        $apiKeys = array_filter($app->getConfig()->subset(Param::class)->get('api-keys'));
        $apiKeys = array_merge($apiKeys, $this->optionalKeys);

        // If your app have some form of user provider you can use an user api token to the list
        if ($app->getServicesFactory()->has('user')) {
            $apiKeys = array_merge($apiKeys, [$app->getServicesFactory()->get('user')->getApiToken()]);
        }

        // If no api keys are provided, we pass the verification
        if (empty($apiKeys)) {
            return;
        }

        /** @var Request $request */
        $request = $app->getRequest();

        if (!$request->hasHeader('Authorization')) {
            throw new NoTokenProvidedException();
        }

        if (!\in_array($request->getHeaderLine('Authorization'), $apiKeys, true)) {
            throw new InvalidTokenException();
        }
    }
}
