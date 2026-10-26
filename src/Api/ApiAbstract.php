<?php

/**
 * Copyright (C) 2023 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Rhyme\SlatePages\Api;

use Rhyme\SlatePages\Client as SlateClient;


/**
 * Class ApiAbstract
 * @package Rhyme\SlatePages\Api
 */
class ApiAbstract
{
    /**
     * @var SlateClient
     */
    protected static SlateClient $client;

    /**
     * ApiAbstract constructor.
     */
    public function __construct() {
        static::initialize();
    }

    /**
     * Initialize
     */
    public static function initialize() {
        self::$client = (self::$client ?? null) !== null ? self::$client : new SlateClient();
    }

}