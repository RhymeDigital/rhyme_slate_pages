<?php

/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages;

use Exception;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;
use Rhyme\SlatePages\Config;


/**
 * Class Client
 * @package Rhyme\SlatePages
 */
class Client extends GuzzleClient
{

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     * @throws Exception
     */
    public function request($method, $uri = '', array $options = [])
    {
        $uri = \strpos($uri, '/') === 0 ? \substr($uri, 1) : $uri;

        // If this is the first time, use the email address/password to get an initial JWT token
        if (!Config::getParameter(Config::TOKEN_KEY))
        {
            if (!($username = Config::getParameter(Config::USERNAME_KEY))) {
                throw new Exception('Missing username in '.Config::ENV_TOKEN_FILE);
            }
            if (!($password = Config::getParameter(Config::PASSWORD_KEY))) {
                throw new Exception('Missing password in '.Config::ENV_TOKEN_FILE);
            }

            // Get a token from the Auth API
            $tokenResponse = parent::request('post', Config::HOST . 'auth/tokens', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => \json_encode(
                    [
                        'email'     => $username,
                        'password'  => $password,
                    ]
                )
            ]);

            // Got a token, store it!
            if (!empty($arrAuth = $tokenResponse->getHeader('Authorization')))
            {
                Config::setParameter(Config::TOKEN_KEY, $arrAuth[0]);
                Config::saveToDotEnv();
            }
        }

        // Now we have a token, do the request
        $response = parent::request($method, Config::HOST . $uri, \array_merge([
            'headers' => [
                'Content-Type'      => 'application/json',
                'Authorization'     => Config::getParameter(Config::TOKEN_KEY),
            ],
        ], $options));

        // Make sure we store any new tokens here too
        if (!empty($arrAuth = $response->getHeader('Authorization')))
        {
            Config::setParameter(Config::TOKEN_KEY, $arrAuth[0]);
            Config::saveToDotEnv();
        }

        return $response;
    }


}