<?php

namespace Adeliom\Lumberjack\Webpack;

class AssetManager
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $entrypoints;

    /**
     * @var array
     */
    private static $filesMap = [];

    /**
     * @var array
     */
    private $manifest;

    /**
     * @var string
     */
    private static $DEFAULT_DIRECTORY;

    public function __construct($directory = null)
    {
        $this->directory = $directory ?? $this->getDefaultDirectory();
    }

    public static function setDefaultDirectory($directory): void
    {
        self::$DEFAULT_DIRECTORY = $directory;
    }

    /**
     * @return string
     */
    private function getDefaultDirectory(): string
    {
        if (isset(self::$DEFAULT_DIRECTORY)) {
            return self::$DEFAULT_DIRECTORY;
        }

        $templateDirectory = get_template_directory();

        return \rtrim($templateDirectory, \DIRECTORY_SEPARATOR) .
            \DIRECTORY_SEPARATOR .
            'build';
    }

    /**
     * @return array
     */
    private function getEntrypoints(): array
    {
        if (null === $this->entrypoints) {
            $file =
                $this->directory . \DIRECTORY_SEPARATOR . 'entrypoints.json';
            $content = \file_get_contents($file);
            if (false === $content) {
                throw new \RuntimeException(
                    \sprintf('Unable to read file "%s"', $file)
                );
            }

            $json = \json_decode($content, true);
            if (\JSON_ERROR_NONE !== \json_last_error()) {
                throw new \RuntimeException(
                    \sprintf('Unable to decode json file "%s"', $file)
                );
            }

            $this->entrypoints = $json['entrypoints'];
        }
        return $this->entrypoints;
    }

    /**
     * @return array
     */
    private function getManifest(): array
    {
        if (null === $this->manifest) {
            $file = $this->directory . \DIRECTORY_SEPARATOR . 'manifest.json';
            $content = \file_get_contents($file);
            if (false === $content) {
                throw new \RuntimeException(
                    \sprintf('Unable to read file "%s"', $file)
                );
            }

            $json = \json_decode($content, true);
            if (\JSON_ERROR_NONE !== \json_last_error()) {
                throw new \RuntimeException(
                    \sprintf('Unable to decode json file "%s"', $file)
                );
            }

            $this->manifest = $json;
        }

        return $this->manifest;
    }

    /**
     * @param string $entrypoint
     * @return array
     */
    public function jsFiles(string $entrypoint): array
    {
        if (!\array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entrypoint)
            );
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!\array_key_exists('js', $files)) {
            return [];
        }

        return $files['js'];
    }

    /**
     * @param string $entrypoint
     * @return array
     */
    public function cssFiles(string $entrypoint): array
    {
        if (!\array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entrypoint)
            );
        }

        $files = $this->getEntrypoints()[$entrypoint];
        if (!\array_key_exists('css', $files)) {
            return [];
        }

        return $files['css'];
    }

    /**
     * @param string $entrypoint
     */
    public function scriptTags(string $entrypoint): void
    {
        $files = $this->jsFiles($entrypoint);
        foreach ($files as $file) {
            if (empty(static::$filesMap[$file])) {
                printf('<script src="%s"></script>', self::getUrl($file));
                static::$filesMap[$file] = true;
            }
        }
    }

    /**
     * @param string $entrypoint
     */
    public function linkTags(string $entrypoint): void
    {
        $files = $this->cssFiles($entrypoint);
        foreach ($files as $file) {
            if (empty(static::$filesMap[$file])) {
                printf('<link rel="stylesheet" href="%s">', self::getUrl($file));
                static::$filesMap[$file] = true;
            }
        }
    }

    /**
     * @param string $resource
     * @return string
     */
    public function asset(string $resource): string
    {
        $withoutLeadingSlash = \ltrim($resource, '/');
        $manifest = $this->getManifest();
        if (isset($manifest[$resource])) {
            return self::getUrl($manifest[$resource]);
        }
        if (isset($manifest[$withoutLeadingSlash])) {
            return self::getUrl($manifest[$withoutLeadingSlash]);
        }

        return self::getUrl($resource);
    }

    private function register(string $name, string $entrypoint, array $config): array
    {
        $config = self::normalizeAssetConfig($config);

        $assets = $this->getAssets($name, $entrypoint, $config);

        $jses = $assets['js'];
        $csses = $assets['css'];

        $js_deps = [];
        $css_deps = [];

        if ($config['js']) {
            foreach ($jses as $js) {
                \wp_register_script(
                    $js['handle'],
                    $js['url'],
                    array_merge($config['js_dep'], $js_deps),
                    $config['version'],
                    $config['in_footer']
                );
                // The next one depends on this one
                $js_deps[] = $js['handle'];
            }
        }

        // Register CSS files
        if ($config['css']) {
            foreach ($csses as $css) {
                \wp_register_style(
                    $css['handle'],
                    $css['url'],
                    array_merge($config['css_dep'], $css_deps),
                    $config['version'],
                    $config['media']
                );
                // The next one depends on this one
                $css_deps[] = $css['handle'];
            }
        }

        return $assets;
    }

    public function enqueue(string $name, string $entrypoint, array $config = []): array
    {
        $config = self::normalizeAssetConfig($config);

        $assets = $this->register($name, $entrypoint, $config);

        $jses = $assets['js'];
        $csses = $assets['css'];

        if ($config['js']) {
            foreach ($jses as $js) {
                \wp_enqueue_script($js['handle']);
            }
        }

        if ($config['css']) {
            foreach ($csses as $css) {
                \wp_enqueue_style($css['handle']);
            }
        }

        return $assets;
    }

    private static function normalizeAssetConfig(array $config): array
    {
        return wp_parse_args(
            $config,
            [
                'version' => null,
                'js' => true,
                'css' => true,
                'js_dep' => [],
                'css_dep' => [],
                'in_footer' => true,
                'media' => 'all',
            ]
        );
    }

    private function getAssets(string $name, string $entrypoint, array $config): array
    {
        $config = self::normalizeAssetConfig($config);

        if (!\array_key_exists($entrypoint, $this->getEntrypoints())) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid entrypoint "%s"', $entrypoint)
            );
        }

        $files = $this->getEntrypoints()[$entrypoint];

        $js = [];
        $css = [];

        if ($config['js'] && isset($files['js']) && count((array) $files['js'])) {
            foreach ($files['js'] as $index => $url) {
                $js[] = [
                    'handle' => self::getHandle($name, $url, 'script'),
                    'url' => self::getUrl($url),
                ];
            }
        }

        if ($config['css'] && isset($files['css']) && count((array) $files['css'])) {
            foreach ($files['css'] as $index => $url) {
                $css[] = [
                    'handle' => self::getHandle($name, $url, 'style'),
                    'url' => self::getUrl($url),
                ];
            }
        }

        return [
            'css' => $css,
            'js' => $js,
        ];
    }

    private static function getUrl(string $asset): string
    {
        $themeUrl = get_theme_file_uri();
        $asset = str_replace(parse_url($themeUrl, PHP_URL_PATH), "", $asset);
        return get_theme_file_uri($asset);
    }

    private static function getHandle(string $name, string $path, string $type = 'script'): string
    {
        if (!\in_array($type, ['script', 'style'], true)) {
            throw new \InvalidArgumentException('Type has to be either script or style.');
        }

        return 'symfony_encore_'
            . $name
            . '_'
            . $path
            . '_'
            . $type;
    }
}
