<?php


namespace Felipetti\Captcha\Data;


use Illuminate\Support\Facades\File;


class Data
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
     * This gets config file name
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
     * This gets the path of the config file, if it was published or not.
     *
     * @param string $publishPath
     * @param string $sourcePath
     * @return string
     */
    public function getProperPath(string $publishPath, string $sourcePath): string
    {
        return File::exists($publishPath) ? $publishPath : $sourcePath;
    }


    /**
     * This get proper config, whether is published or not.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
