<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Api\Slate;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use Rhyme\SlatePages\Api\ApiAbstract;
use Rhyme\SlatePages\Utils;
use Rhyme\SlatePages\Client;
use Rhyme\SlatePages\Api\Account\Account as AccountApi;
use Rhyme\SlatePages\Exception\ValidationFailedException;

/**
 * Class Record
 * @package Rhyme\SlatePages\Api\Slate
 */
class Record extends ApiAbstract
{

    /**
     * @param string $accountUuid
     * @param string $fieldUuid
     * @return ResponseInterface|null
     */
    public static function getEntries(string $accountUuid, string $fieldUuid): ?ResponseInterface
    {
        static::initialize();
        return static::$client->get("accounts/$accountUuid/slate/fields/$fieldUuid/record-entries");
    }


    /**
     * @param string $accountUuid
     * @param string $fieldUuid
     * @return ResponseInterface|null
     */
    public static function create(string $accountUuid, string $fieldUuid): ?ResponseInterface
    {
        static::initialize();
        return static::$client->post("accounts/$accountUuid/slate/fields/$fieldUuid/record-entries");
    }
}