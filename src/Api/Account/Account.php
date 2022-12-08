<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Api\Account;

use Psr\Http\Message\ResponseInterface;
use Rhyme\SlatePages\Client;
use Rhyme\SlatePages\Api\ApiAbstract;

/**
 * Class Account
 * @package Rhyme\SlatePages\Api\Account
 */
class Account extends ApiAbstract
{

    /**
     * @param $accountUUID
     * @return ResponseInterface|null
     */
    public static function getOne($accountUUID): ?ResponseInterface
    {
        static::initialize();
        return static::$client->get("accounts/$accountUUID");
    }
}