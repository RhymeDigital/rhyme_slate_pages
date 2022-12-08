<?php

namespace Rhyme\SlatePages;

if (!\class_exists('Composer\Autoload\ClassLoader')) {
    require_once \realpath(__DIR__ . '/vendor/autoload.php');
}

use Exception;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

class Config
{
    public const USERNAME_KEY = 'SLATE_PAGES_USERNAME';

    public const PASSWORD_KEY = 'SLATE_PAGES_PASSWORD';

    public const TOKEN_KEY = 'SLATE_PAGES_ACCESS_TOKEN';

    public const HOST = 'https://api.slatepages.com/v2.0/';

    public const ENV_TOKEN_FILE = '.env.slate_pages_token';

    /**
     * @var string
     */
    protected static string $username;

    /**
     * @var string
     */
    protected static string $password;

    /**
     * @var string
     */
    protected static string $projectDir;

    /**
     * @var array
     */
    protected static array $parameters;

    /**
     * @return void
     * @throws Exception
     */
    public static function loadDotEnv(): void
    {
        // Create .env file if it doesn't already exist
        if (!\file_exists($envPath = static::getProjectDir().'/'.static::ENV_TOKEN_FILE)) {
            \fopen($envPath, 'w');
            \fclose($envPath);
        }

        if (isset($parameters)) {
            return;
        }

        $dotenv = new Dotenv();
        $dotenv->usePutenv(false);

        $parameters = $dotenv->parse(\file_get_contents($envPath));

        if (0 !== \count($parameters)) {
            static::$parameters = \array_merge(static::$parameters ?? [], $parameters);
        }
    }

    /**
     * Write the parameters to our .env.slate_pages_token file
     * @return void
     * @throws Exception
     */
    public static function saveToDotEnv(): void
    {
        // Write to a temp file first
        $strTemp = static::getProjectDir().'/'.\md5(\uniqid(\mt_rand(), true));
        $objFile = \fopen($strTemp, 'w');

        $buffer = '';
        foreach (static::$parameters as $key=>$val) {
            $buffer .= $key.'="'.$val.'"'."\n"; // Todo: check the characters/encoding here!
        }
        fwrite($objFile, $buffer);
        fclose($objFile);

        // Make sure the file has been written
        if (!\filesize($strTemp)) {
            throw new Exception('The Slate Pages token file could not be written. Have you reached your quota limit?');
        }

        $fs = new Filesystem();

        // Adjust the file permissions
        $fs->chmod($strTemp, 0666 & ~umask());

        $strDestination = static::getProjectDir().'/'.static::ENV_TOKEN_FILE;

        // Get the realpath in case it is a symlink (see #2209)
        if ($realpath = \realpath($strDestination))
        {
            $strDestination = $realpath;
        }

        // Then move the file to its final destination
        $fs->rename($strTemp, $strDestination, true);

        // Reset the Zend OPcache
        if (\function_exists('opcache_invalidate'))
        {
            \opcache_invalidate($strDestination, true);
        }

        // Recompile the APC file (thanks to Trenker)
        if (\function_exists('apc_compile_file') && !ini_get('apc.stat'))
        {
            \apc_compile_file($strDestination);
        }

        // Remove the temp file
        $fs->remove($strTemp);
    }

    /**
     * @return string
     */
    public static function getProjectDir(): string
    {
        if (isset(static::$projectDir)) {
            return static::$projectDir;
        }
        
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        return static::$projectDir = \dirname(\dirname(\dirname($reflection->getFileName())));
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public static function getParameter(string $name)
    {
        self::loadDotEnv();
        return isset(static::$parameters) ? static::$parameters[$name] : null;
    }

    /**
     * @param string $name
     * @param $value
     * @return void
     * @throws Exception
     */
    public static function setParameter(string $name, $value): void
    {
        self::loadDotEnv();
        static::$parameters = static::$parameters ?? [];
        static::$parameters[$name] = $value;
    }

    /**
     * @param array $params
     * @return void
     * @throws Exception
     */
    public static function setParameters(array $params): void
    {
        self::loadDotEnv();
        foreach ($params as $name => $value) {
            static::setParameter($name, $value);
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public static function unsetParameter(string $name): void
    {
        static::$parameters = static::$parameters ?? [];
        unset(static::$parameters[$name]);
    }
}