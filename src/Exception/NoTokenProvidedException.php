<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 16/04/2018
 * Time: 14:23
 */

namespace Yoctu\ApiGuardian\Exception;

class NoTokenProvidedException extends \LogicException
{
    public function __construct(string $message = 'A valid api token must be provided.', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
