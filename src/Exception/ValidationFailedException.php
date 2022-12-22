<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Exception;

use GuzzleHttp\Exception\ClientException;
use Rhyme\SlatePages\Utils as SlatePagesUtils;

/**
 * Class ValidationFailedException
 */
class ValidationFailedException extends ClientException
{

    /**
     * Will indicate if any validation errors have occurred. It is really
     * just a logical or against the hasSchemaErrors and hasDataErrors attributes
     *
     * @return bool|null
     */
    public function hasErrors() : ?bool
    {
        $hasErrors = null;

        if ($response = $this->getResponse()) {
            $responseData = SlatePagesUtils::convertResponseToArray($response);
            if (SlatePagesUtils::isNotEmptyArray($responseData) &&
                SlatePagesUtils::isNotEmptyArray($responseData['validationErrors'])) {
                $hasErrors = \boolval($responseData['validationErrors']['hasErrors']);
            }
        }

        return $hasErrors;
    }


    /**
     * Will indicate if there are data-related errors, such as length requirements
     *
     * @return bool|null
     */
    public function hasDataErrors() : ?bool
    {
        $hasErrors = null;

        if ($response = $this->getResponse()) {
            $responseData = SlatePagesUtils::convertResponseToArray($response);
            if (SlatePagesUtils::isNotEmptyArray($responseData) &&
                SlatePagesUtils::isNotEmptyArray($responseData['validationErrors'])) {
                $hasErrors = \boolval($responseData['validationErrors']['hasDataErrors']);
            }
        }

        return $hasErrors;
    }


    /**
     * Will indicate if there are schema errors with the data provided. This can
     * happen if the request has an incorrectly formatted message body
     *
     * @return bool|null
     */
    public function hasSchemaErrors() : ?bool
    {
        $hasErrors = null;

        if ($response = $this->getResponse()) {
            $responseData = SlatePagesUtils::convertResponseToArray($response);
            if (SlatePagesUtils::isNotEmptyArray($responseData) &&
                SlatePagesUtils::isNotEmptyArray($responseData['validationErrors'])) {
                $hasErrors = \boolval($responseData['validationErrors']['hasDataErrors']);
            }
        }

        return $hasErrors;
    }


    /**
     * If hasSchemaErrors is true this will contain details about what errors had occurred
     *
     * @return array
     */
    public function getSchemaErrors() : array
    {
        $errors = [];

        if ($response = $this->getResponse()) {
            $responseData = SlatePagesUtils::convertResponseToArray($response);
            if (SlatePagesUtils::isNotEmptyArray($responseData) &&
                SlatePagesUtils::isNotEmptyArray($responseData['validationErrors'])) {
                $errors = (array) $responseData['validationErrors']['schemaErrors'];
            }
        }

        return $errors;
    }


    /**
     * This is where typical data-related validation errors will occur
     *
     * @return array
     */
    public function getDataErrors() : array
    {
        $errors = [];

        if ($response = $this->getResponse()) {
            $responseData = SlatePagesUtils::convertResponseToArray($response);
            if (SlatePagesUtils::isNotEmptyArray($responseData) &&
                SlatePagesUtils::isNotEmptyArray($responseData['validationErrors'])) {
                $errors = (array) $responseData['validationErrors']['dataErrors'];
            }
        }

        return $errors;
    }
}