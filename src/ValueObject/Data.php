<?php

namespace Felipetti\Captcha\ValueObject;

use Illuminate\Support\Facades\File;

final class Data
{

    // This has the file name of the config file.
    private string $configFileName = 'captcha.php';

    // This contains the path of the config file if it was published.
    private string $publishPath;

    // This contains the source of the config file.
    private string $sourcePath;

    // This has the config file, whether it was published or not.
    private array $config;


    /**
     * This method charges all the necessary attributes to make the package work.
     */
    public function __construct()
    {
        $this->publishPath = config_path($this->configFileName);
        $localPath = str_replace([base_path(), 'config'], ['', 'Config'], $this->publishPath);
        $this->sourcePath = dirname(__FILE__, 2) . $localPath;

        $this->config = include($this->getProperPath($this->publishPath, $this->sourcePath));
    }

    /**
     * This gets the path of the config file, if it was published or not.
     *
     * @param string $publishPath
     * @param string $sourcePath
     * @return string
     */
    private function getProperPath(string $publishPath, string $sourcePath): string
    {
        return File::exists($publishPath) ? $publishPath : $sourcePath;
    }

    /**
     * This gets config file name.
     *
     * @return string
     */
    public function getConfigFileName(): string
    {
        return $this->configFileName;
    }


    /**
     * This gets source config file path.
     *
     * @return string
     */
    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }


    /**
     * This gets published config file path.
     *
     * @return string
     */
    public function getPublishPath(): string
    {
        return $this->publishPath;
    }

    /**
     * This gets the URL from the proper config.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->config['url'];
    }

    /**
     * This gets the threshold from the proper config (Only for CAPTCHA V3).
     *
     * @return float
     */
    public function getThreshold(): float
    {
        return match(true){
            is_null($this->config['threshold']), $this->config['threshold'] < 0.1 => 0.1,
            $this->config['threshold'] > 1 => 1,
            default => $this->config['threshold']
        };
    }

    /**
     * This gets secret key or test secret key, according to the config file and if the application
     * is in production or not.
     *
     * @return string|NULL
     */
    public function getSecretKey(): ?string
    {
        $secretKey = $this->config['secret_key'];

        return empty($this->config['enable_test_key']) ?
            $secretKey :
            (app()->isProduction() ? $secretKey : $this->config['test_secret_key']);
    }

    /**
     * This selects the corresponding response of the error, according to the config file.
     * If there is no error matching, it will show a default error.
     *
     * @param string $errorCode
     * @return string
     */
    public function getErrorMessage(string $errorCode): string
    {
        $errorCodes = $this->config['error_codes'];

        return $errorCodes[$errorCode] ?? $errorCodes['default'];
    }
}
