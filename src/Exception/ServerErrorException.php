<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Exception;

use GuzzleHttp\Exception\ServerException;
use Rhyme\SlatePages\Utils;

/**
 * Class ServerErrorException
 */
class ServerErrorException extends ServerException
{

    /**
     * @return int
     */
    public function getErrorCode() : int
    {
        $code = -1;

        if ($response = $this->getResponse()) {
            $responseData = Utils::convertResponseToArray($response);
            if (!empty($responseData)) {
                $code = \intval($responseData['errorCode']);
            }
        }

        return $code;
    }

    /**
     * @return string
     */
    public function getErrorDebugDescription() : string
    {
        $desc = '';

        if ($response = $this->getResponse()) {
            $responseData = Utils::convertResponseToArray($response);
            if (!empty($responseData)) {
                $desc = \strval($responseData['errorDebugDescription']);
            }
        }

        return $desc;
    }
}