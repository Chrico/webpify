<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Assets;

/**
 * @package WebPify\Assets
 */
final class LazyLoadScript
{

    const HANDLE = 'WebPify';

    /**
     * @var string
     */
    private $pluginFilePath;

    /**
     * @var LazyLoadScriptData
     */
    private $data;

    /**
     * @param string $pluginFilePath
     * @param LazyLoadScriptData $data
     */
    public function __construct(string $pluginFilePath, LazyLoadScriptData $data)
    {
        $this->pluginFilePath = $pluginFilePath;
        $this->data = $data;
    }

    public function enqueue()
    {
        wp_enqueue_script(
            self::HANDLE,
            $this->url(),
            [],
            null,
            true
        );

        wp_localize_script(
            self::HANDLE,
            self::HANDLE,
            ['options' => $this->data->options()]
        );
    }

    /**
     * @return string
     */
    private function url(): string
    {
        $fileName = self::HANDLE.($this->isDebug()
                ? '.js'
                : '.min.js');
        $subdir = '/assets/js/dist';

        $url = plugins_url("{$subdir}/{$fileName}", $this->pluginFilePath);

        return $url;
    }

    /**
     * @return bool
     */
    private function isDebug(): bool
    {
        return defined('SCRIPT_DEBUG')
            ? SCRIPT_DEBUG
            : (defined('WP_DEBUG') && WP_DEBUG);
    }

    /**
     * @param string $tag
     * @param string $handle
     *
     * @return string
     */
    public function printInline(string $tag, string $handle): string
    {
        if ($handle === self::HANDLE) {
            $src = str_replace(
                home_url('/wp-content'),
                WP_CONTENT_DIR,
                wp_scripts()->registered[$handle]->src
            );

            $content = file_get_contents($src);
            if (! ! $content) {
                $tag = sprintf(
                    "<script>%s</script>",
                    $content
                );
            }
        }

        return $tag;
    }
}
