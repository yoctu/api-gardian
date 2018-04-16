<?php

namespace Yoctu\Apiguardian;

use ObjectivePHP\Application\ApplicationInterface;
use ObjectivePHP\Application\Config\Param;
use Yoctu\Apiguardian\Exception\InvalidTokenException;
use Yoctu\Apiguardian\Exception\NoTokenProvidedException;
use Zend\Diactoros\Request;

/**
 * Class Apiguardian
 * @package Yoctu\Apiguardian
 */
class Apiguardian
{
    /**
     * @param ApplicationInterface $app
     *
     * @throws \Yoctu\Apiguardian\Exception\InvalidTokenException
     * @throws  \Yoctu\Apiguardian\Exception\NoTokenProvidedException
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
