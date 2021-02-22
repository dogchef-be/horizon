<?php

namespace Laravel\Horizon;

use Closure;
use Exception;
use Illuminate\Support\Facades\File;
use RuntimeException;
use stdClass;

class Horizon
{
    /**
     * The callback that should be used to authenticate Horizon users.
     *
     * @var \Closure
     */
    public static $authUsing;

    /**
     * The Slack notifications webhook URL.
     *
     * @var string
     */
    public static $slackWebhookUrl;

    /**
     * The Slack notifications channel.
     *
     * @var string
     */
    public static $slackChannel;

    /**
     * The SMS notifications phone number.
     *
     * @var string
     */
    public static $smsNumber;

    /**
     * The email address for notifications.
     *
     * @var string
     */
    public static $email;

    /**
     * Indicates if Horizon should use the dark theme.
     *
     * @var bool
     */
    public static $useDarkTheme = false;

    /**
     * The database configuration methods.
     *
     * @var array
     */
    public static $databases = [
        'Jobs', 'Supervisors', 'CommandQueue', 'Tags',
        'Metrics', 'Locks', 'Processes',
    ];

    /**
     * Determine if the given request can access the Horizon dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    /**
     * Set the callback that should be used to authenticate Horizon users.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    /**
     * Configure the Redis databases that will store Horizon data.
     *
     * @param  string  $connection
     * @return void
     * @throws Exception
     */
    public static function use($connection)
    {
        if (! is_null($config = config("database.redis.clusters.{$connection}.0"))) {
            config(["database.redis.{$connection}" => $config]);
        } elseif (is_null($config) && is_null($config = config("database.redis.{$connection}"))) {
            throw new Exception("Redis connection [{$connection}] has not been configured.");
        }

        config(['database.redis.horizon' => array_merge($config, [
            'options' => ['prefix' => config('horizon.prefix') ?: 'horizon:'],
        ])]);
    }

    /**
     * Specifies that Horizon should use the dark theme.
     *
     * @return static
     */
    public static function night()
    {
        static::$useDarkTheme = true;

        return new static;
    }

    /**
     * Get the default JavaScript variables for Horizon.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'path' => config('horizon.path'),
            'scheduler' => base_path('scheduler.json'),
            'controllers' => self::getControllersTree(base_path('app/Http/Controllers/Jobs'))
        ];
    }
    /**
     * Specify the email address to which email notifications should be routed.
     *
     * @param  string  $email
     * @return static
     */
    public static function routeMailNotificationsTo($email)
    {
        static::$email = $email;

        return new static;
    }

    /**
     * Specify the webhook URL and channel to which Slack notifications should be routed.
     *
     * @param  string  $url
     * @param  string  $channel
     * @return static
     */
    public static function routeSlackNotificationsTo($url, $channel = null)
    {
        static::$slackWebhookUrl = $url;
        static::$slackChannel = $channel;

        return new static;
    }

    /**
     * Specify the phone number to which SMS notifications should be routed.
     *
     * @param  string  $number
     * @return static
     */
    public static function routeSmsNotificationsTo($number)
    {
        static::$smsNumber = $number;

        return new static;
    }

    /**
     * Determine if Horizon's published assets are up-to-date.
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    public static function assetsAreCurrent()
    {
        $publishedPath = public_path('vendor/horizon/mix-manifest.json');

        if (! File::exists($publishedPath)) {
            throw new RuntimeException('Horizon assets are not published. Please run: php artisan horizon:publish');
        }

        return File::get($publishedPath) === File::get(__DIR__ . '/../public/mix-manifest.json');
    }
        
    /**
     * Get all dire
     *
     * @param  mixed $path
     * @param  mixed $replace
     * @param  mixed $root
     * @return stdClass
     */
    private static function getControllersTree($path) {
        $folders = glob("{$path}/*", GLOB_ONLYDIR);

        if ($folders === false || count($folders) === 0) {
            return null;
        }

        $result  = new stdClass();

        foreach ($folders as $folder) {
            $current = substr($folder, strlen("{$path}/"));
            
            $aux = self::getPHPFiles($folder);

            if (isset($aux)) {
                $result->{$current} = $aux;
            }
        }

        return $result;
    }

    private static function getPHPFiles($path) {
        $files = glob("{$path}/*.php");

        if ($files === false || count($files) === 0) {
            return null;
        }

        $result = new stdClass();

        foreach ($files as $file) {
            $current = substr(substr($file, strlen("{$path}/")), 0, -4);
            $methods = self::getPHPFileMethods($file);

            if (count($methods) > 0) {
                $result->{$current} = new stdClass();
                $result->{$current}->jobs = $methods;
                $result->{$current}->namespace = substr(preg_replace('/.*\/app/', 'App', $file), 0, -4);
            }
        }

        return $result;
    }

    private static function getPHPFileMethods($path, $sort = true) {
        $file = file($path);

        $result = [];

        foreach ($file as $line) {
            $line = trim($line);

            if (stripos($line, 'function ') !== false) {
                $function_name = trim(str_ireplace(['public', 'private', 'protected', 'static'], '', $line));

                $function_name = trim(substr($function_name, 9, strpos($function_name, '(') - 9));

                if (!in_array($function_name, ['__construct', '__destruct', '__get', '__set', '__isset', '__unset'])) {
                    $result[] = $function_name;
                }
            }
        }
        
        asort($result);
        
        return array_values($result);
    }
}
