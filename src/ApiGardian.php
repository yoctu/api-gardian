<?php

namespace Yoctu\ApiGardian;

use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Config\Param;
use Yoctu\ApiGardian\Exception\InvalidTokenException;
use Yoctu\ApiGardian\Exception\NoTokenProvidedException;
use Zend\Diactoros\Request;

/**
 * Class ApiGardian
 * @package Yoctu\ApiGardian
 */
class ApiGardian
{
    /**
     * @param ApplicationInterface $app
     *
     * @throws \Yoctu\ApiGardian\Exception\InvalidTokenException
     * @throws  \Yoctu\ApiGardian\Exception\NoTokenProvidedException
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
