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
    /**
     * @param ApplicationInterface $app
     *
     * @throws \Yoctu\ApiGuardian\Exception\InvalidTokenException
     * @throws  \Yoctu\ApiGuardian\Exception\NoTokenProvidedException
     */
    public function __invoke(ApplicationInterface $app)
    {
        $apiKeys = array_filter($app->getConfig()->subset(Param::class)->get('api-keys'));

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
