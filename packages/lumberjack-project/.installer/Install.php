<?php

namespace Adeliom\Installer;

use Composer\Script\Event;
use Symfony\Component\Dotenv\Dotenv;

class Install
{
    const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"#$%&()*+,-./:;<=>?@[]^_`{|}~';

    public static function setPackageKeys(Event $event){
        if(!is_file(dirname(__DIR__) . "/.env")) {
            $projectENV = file_get_contents(dirname(__DIR__) . "/.env.example");
            file_put_contents(dirname(__DIR__) . "/.env", $projectENV);
        }else{
            $projectENV = file_get_contents(dirname(__DIR__) . "/.env");
        }

        foreach ([
            "ACF_PRO_KEY" => base64_decode("YjNKa1pYSmZhV1E5TkRBMk9UWjhkSGx3WlQxa1pYWmxiRzl3WlhKOFpHRjBaVDB5TURFMExUQTVMVEkySURFek9qVXpPakkx"),
            "GRAVITYFORMS_KEY" => base64_decode("MDYxNjhkYzA2YzQ1NWRjYWVkNmU0NzM2Y2EzNTBkMjg=")
         ] as $k => $v){
            $projectENV = preg_replace('/^'.$k.'=?.+$/m', $k . '=' . $v, $projectENV);
        }
        file_put_contents(dirname(__DIR__) . "/.env", $projectENV);
    }

    public static function buildEnv(Event $event)
    {
        if(!is_file(dirname(__DIR__) . "/.env")) {
            $projectENV = file_get_contents(dirname(__DIR__) . "/.env.example");
            file_put_contents(dirname(__DIR__) . "/.env", $projectENV);
        }else{
            $projectENV = file_get_contents(dirname(__DIR__) . "/.env");
        }

        if(!is_file(dirname(__DIR__) . "/install.lock")){
            $salts = self::generateSalts();
            $appKey = self::generateAppKey();
            $projectVars = [
                "APP_KEY" => $appKey
            ];

            $projectVars = array_merge($projectVars, $salts);
        }else{
            $projectVars = json_decode(file_get_contents(dirname(__DIR__) . "/.installer/install.lock"));
        }

        foreach (array_merge($projectVars, ["ACF_PRO_KEY" => base64_decode("YjNKa1pYSmZhV1E5TkRBMk9UWjhkSGx3WlQxa1pYWmxiRzl3WlhKOFpHRjBaVDB5TURFMExUQTVMVEkySURFek9qVXpPakkx")]) as $k => $v){
            $projectENV = preg_replace('/^'.$k.'=?.+$/m', $k . '=' . $v, $projectENV);
        }
        file_put_contents(dirname(__DIR__) . "/.env", $projectENV);


        if(!is_file(dirname(__DIR__) . "/.installer/install.lock")) {
            file_put_contents(dirname(__DIR__) . "/.installer/install.lock", json_encode($projectVars, JSON_PRETTY_PRINT));
        }

        if(getenv('LANDO_INFO')){
            $lando = json_decode(getenv('LANDO_INFO'), TRUE);
            $siteURL = sprintf('%s.%s', getenv('LANDO_APP_NAME'), getenv('LANDO_DOMAIN'));
            $dbHost = $lando["database"]["internal_connection"]["host"];
            $dbName = $lando["database"]["creds"]["database"];
            $dbUser = $lando["database"]["creds"]["password"];
            $dbPassword = $lando["database"]["creds"]["user"];

            $localVars = [
                "DB_HOST" => $dbHost,
                "DB_NAME" => $dbName,
                "DB_USER" => $dbUser,
                "DB_PASSWORD" => $dbPassword,
                "WP_HOME" => "https://" . $siteURL,
            ];

            if(!is_file(dirname(__DIR__) . "/.env.local")) {
                file_put_contents(dirname(__DIR__) . "/.env.local", implode('='.PHP_EOL, array_keys($localVars)) . '='.PHP_EOL);
            }
            $localENV = file_get_contents(dirname(__DIR__) . "/.env.local");
            foreach ($localVars as $k => $v){
                $localENV = preg_replace('/^'.$k.'=?.+$/m', $k . '=' . $v, $localENV);
            }
            file_put_contents(dirname(__DIR__) . "/.env.local", $localENV);
        }
    }

    public static function generateSalts(){
        $salts=[];
        $salts['AUTH_KEY'] = self::salt();
        $salts['SECURE_AUTH_KEY'] = self::salt();
        $salts['LOGGED_IN_KEY'] = self::salt();
        $salts['NONCE_KEY'] = self::salt();
        $salts['AUTH_SALT'] = self::salt();
        $salts['SECURE_AUTH_SALT'] = self::salt();
        $salts['LOGGED_IN_SALT'] = self::salt();
        $salts['NONCE_SALT'] = self::salt();
        return $salts;
    }

    public static function generateAppKey(){
        return "'".'base64:' . base64_encode(sha1(mt_rand(1, 90000) . self::salt(32)))."'";
    }

    public static function salt($length = 64): string
    {
        $charactersLength = strlen(self::CHARS);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARS[rand(0, $charactersLength - 1)];
        }
        return "'".$randomString."'";
    }

    public static function dumpEnv(Event $event)
    {
        $io = $event->getIO();

        $path = dirname(__DIR__).'/.env';

        $vars = self::loadEnv($path, null);
        $env = $vars['APP_ENV'];

        $vars = var_export($vars, true);
        $vars = <<<EOF
<?php

// This file was generated by running "composer dump-env $env"

return $vars;

EOF;
        file_put_contents($path.'.local.php', $vars, \LOCK_EX);

        $io->writeError('Successfully dumped .env files in <info>.env.local.php</>');

        return 0;
    }

    private static function loadEnv(string $path, ?string $env): array
    {
        if (!file_exists($autoloadFile = dirname(__DIR__).'/vendor/autoload.php')) {
            throw new \RuntimeException(sprintf('Please run "composer install" before running this command: "%s" not found.', $autoloadFile));
        }

        require $autoloadFile;

        if (!class_exists(Dotenv::class)) {
            throw new \RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
        }

        $globalsBackup = [$_SERVER, $_ENV];
        unset($_SERVER['APP_ENV']);
        $_ENV = ['APP_ENV' => $env];
        $_SERVER['SYMFONY_DOTENV_VARS'] = implode(',', array_keys($_SERVER));
        putenv('SYMFONY_DOTENV_VARS='.$_SERVER['SYMFONY_DOTENV_VARS']);

        try {
            $dotenv = new Dotenv();

            if (!$env && file_exists($p = "$path.local")) {
                $env = $_ENV['APP_ENV'] = $dotenv->parse(file_get_contents($p), $p)['APP_ENV'] ?? null;
            }

            if (!$env) {
                throw new \RuntimeException('Please provide the name of the environment either by using the "--env" command line argument or by defining the "APP_ENV" variable in the ".env.local" file.');
            }

            if (method_exists($dotenv, 'loadEnv')) {
                $dotenv->loadEnv($path);
            } else {
                // fallback code in case your Dotenv component is not 4.2 or higher (when loadEnv() was added)
                $dotenv->load(file_exists($path) || !file_exists($p = "$path.dist") ? $path : $p);

                if ('test' !== $env && file_exists($p = "$path.local")) {
                    $dotenv->load($p);
                }

                if (file_exists($p = "$path.$env")) {
                    $dotenv->load($p);
                }

                if (file_exists($p = "$path.$env.local")) {
                    $dotenv->load($p);
                }
            }

            unset($_ENV['SYMFONY_DOTENV_VARS']);
            $env = $_ENV;
        } finally {
            list($_SERVER, $_ENV) = $globalsBackup;
        }

        return $env;
    }
}