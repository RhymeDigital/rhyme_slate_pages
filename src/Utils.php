<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages;

use Psr\Http\Message\ResponseInterface;

class Utils
{
    /**
     * Convert a timestamp to EDM format for datetime filters/queries
     * @param $intTstamp
     * @return string
     */
    public static function convertTimestampToEDM($intTstamp): string
    {
        $intTstamp = intval($intTstamp);
        return date('Y-m-d', $intTstamp).'T'.date('H:i:s', $intTstamp);
    }

    /**
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr): bool
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    /**
     * @param $source
     * @return bool
     */
    public static function isNotEmptyArray($source) : bool
    {
        return !empty($source) && \is_array($source);
    }

    /**
     * @param $fieldTitle
     * @param $arrFields
     * @return int|string
     */
    public static function getFieldUUIDByTitle($fieldTitle, $arrFields): string {
        foreach ($arrFields as $uuid=>$data) {
            if ($data['title'] === $fieldTitle) {
                return $uuid;
            }
        }
        return '';
    }

    /**
     * @param $fieldTitle
     * @param $arrFields
     * @return string
     */
    public static function getFieldValueByTitle($fieldTitle, $arrFields): string {
        foreach ($arrFields as $uuid=>$data) {
            if ($data['title'] === $fieldTitle) {
                return $data['value'];
            }
        }
        return '';
    }

    /**
     * @param array $arrFields
     * @return array
     */
    public static function getFieldTitlesForSlate(array $arrFields): array {
        $arrTitles = [];
        foreach ($arrFields as $uuid=>$data) {
            $arrTitles[] = $data['title'];
        }
        return $arrTitles;
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    public static function convertResponseToArray(ResponseInterface $response): array {
        return \json_decode((string)$response->getBody(), true);
    }
}