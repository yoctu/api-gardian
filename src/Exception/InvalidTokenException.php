<?php
/**
 * Created by PhpStorm.
 * User: jerome
 * Date: 16/04/2018
 * Time: 14:23
 */

namespace Yoctu\ApiGuardian\Exception;

class InvalidTokenException extends \LogicException
{
    public function __construct(string $message = 'Invalid API token provided.', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
