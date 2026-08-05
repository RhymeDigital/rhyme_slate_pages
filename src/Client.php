<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Rhyme\SlatePages\Exception\ServerErrorException;
use Rhyme\SlatePages\Exception\ValidationFailedException;


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
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        $response = null;
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
        try {
            $response = parent::request($method, Config::HOST . $uri, \array_merge([
                'headers' => [
                    'Content-Type'      => 'application/json',
                    'Authorization'     => Config::getParameter(Config::TOKEN_KEY),
                ],
            ], $options));
        }
        catch (ClientException $ce) {

            // See if we can use our custom exception
            if ($ce->getCode() === 422) {
                throw new ValidationFailedException('Validation failed: '.$ce->getMessage(),
                    $ce->getRequest(),
                    $ce->getResponse(),
                    $ce
                );
            }

            throw $ce;
        }
        catch (ServerException $se) {

            // See if we can use our custom exception
            if ($se->getCode() === 500) {
                throw new ServerErrorException('Slate Pages server error: '.$se->getMessage(),
                    $se->getRequest(),
                    $se->getResponse(),
                    $se
                );
            }

            throw $se;
        }

        // Make sure we store any new tokens here too
        if ($response !== null && !empty($arrAuth = $response->getHeader('Authorization')))
        {
            Config::setParameter(Config::TOKEN_KEY, $arrAuth[0]);
            Config::saveToDotEnv();
        }

        return $response;
    }


}