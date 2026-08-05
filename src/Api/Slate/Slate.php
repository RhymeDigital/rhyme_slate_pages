<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Api\Slate;

use Psr\Http\Message\ResponseInterface;
use Rhyme\SlatePages\Api\ApiAbstract;
use Rhyme\SlatePages\Utils;

/**
 * Class Slate
 * @package Rhyme\SlatePages\Api\Slate
 */
class Slate extends ApiAbstract
{

    /**
     * @param $slateUUID
     * @return ResponseInterface|null
     */
    public static function getOne($slateUUID): ?ResponseInterface
    {
        static::initialize();
        return static::$client->get("slates/$slateUUID");
    }

    /**
     * @param string $accountUuid
     * @param array $arrQuery
     * @return ResponseInterface|null
     */
    public static function search(string $accountUuid, array $arrQuery): ?ResponseInterface
    {
        static::initialize();
        return static::$client->get("accounts/$accountUuid/slates", [
            'query' => $arrQuery,
        ]);
    }

    /**
     * @param string $slateUuid
     * @param string $accountUuid
     * @param array $arrData
     * @param array $arrFields
     * @return ResponseInterface|null
     */
    public static function update(string $slateUuid, string $accountUuid, array $arrData, array $arrFields): ?ResponseInterface
    {
        static::initialize();

        $arrBody = [];
        foreach ($arrData as $key=>$val) {
            $arrBody[] = [
                'op'        => 'replace',
                'path'      => '/fields/'.Utils::getFieldUUIDByTitle($key, $arrFields).'/value',
                'value'     => $val,
            ];
        }
        if (empty($arrBody)) {
            return null;
        }

        return static::$client->patch("resources/slate/$slateUuid", [
            'query' => [
                'account-uuid'  => $accountUuid,
            ],
            'body' => json_encode($arrBody)
        ]);
    }
}