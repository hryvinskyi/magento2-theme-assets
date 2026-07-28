<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\ViewModel;

use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Hryvinskyi\ThemeAssets\Api\AssetContentStrategyPoolInterface;
use Hryvinskyi\ThemeAssets\Api\AssetRendererInterface;
use Hryvinskyi\HeadTagManager\Api\HeadTagManagerInterface;

/**
 * ViewModel for rendering scripts, stylesheets and resolving asset file paths
 */
class AssetRenderer extends AssetPathResolver implements AssetRendererInterface
{
    /**
     * @param Repository $assetRepository
     * @param SecureHtmlRenderer $secureRenderer
     * @param HeadTagManagerInterface $headTagManager
     * @param AssetContentStrategyPoolInterface $strategyPool
     */
    public function __construct(
        Repository $assetRepository,
        private readonly SecureHtmlRenderer $secureRenderer,
        private readonly HeadTagManagerInterface $headTagManager,
        private readonly AssetContentStrategyPoolInterface $strategyPool
    ) {
        parent::__construct($assetRepository);
    }

    /**
     * @inheritDoc
     */
    public function renderScript(
        string $fileId,
        string $renderType = self::RENDER_INLINE,
        string $loadStrategy = self::LOAD_SYNC,
        bool $preload = self::PRELOAD_DISABLED,
        array $params = [],
        array $options = []
    ): string {
        $asset = $this->createAsset($fileId, $params);

        if ($renderType === self::RENDER_INLINE) {
            return $this->renderInlineScript($asset, $options);
        }

        return $this->renderExternalScript($asset, $loadStrategy, $preload, $options);
    }

    /**
     * @inheritDoc
     */
    public function renderStyle(
        string $fileId,
        string $renderType = self::RENDER_INLINE,
        bool $preload = self::PRELOAD_DISABLED,
        array $params = [],
        array $options = []
    ): string {
        $asset = $this->createAsset($fileId, $params);

        if ($renderType === self::RENDER_INLINE) {
            return $this->renderInlineStyle($asset, $options);
        }

        return $this->renderExternalStyle($asset, $preload, $options);
    }

    /**
     * @inheritDoc
     */
    public function getContent(string $fileId, array $params = []): string
    {
        $asset = $this->createAsset($fileId, $params);
        return $this->getAssetContent($asset);
    }

    /**
     * Render script tag with inline content
     *
     * @param File $asset The asset file
     * @param array{attributes?: array<string, string>, content_before?: string, content_after?: string} $options Rendering options
     * @return string The rendered script tag
     */
    private function renderInlineScript(File $asset, array $options = []): string
    {
        $content = $this->getAssetContent($asset);

        if ($content === '') {
            return '';
        }

        $attributes = $options['attributes'] ?? [];
        $contentBefore = $options['content_before'] ?? '';
        $contentAfter = $options['content_after'] ?? '';
        $content = $contentBefore . $content . $contentAfter;

        return $this->secureRenderer->renderTag(
            'script',
            $attributes,
            $content,
            false
        );
    }

    /**
     * Render external script using HeadTagManager
     *
     * @param File $asset The asset file
     * @param string $loadStrategy Loading strategy (sync, async, defer)
     * @param bool $preload Whether to add preload resource hint
     * @param array{priority?: string, attributes?: array<string, string>, position?: string} $options Rendering options
     * @return string The rendered <script> tag
     */
    private function renderExternalScript(
        File $asset,
        string $loadStrategy,
        bool $preload,
        array $options = []
    ): string {
        $src = $asset->getUrl();
        $attributes = $options['attributes'] ?? [];
        $attributes['src'] = $src;

        // Add loading strategy attributes
        if ($loadStrategy === self::LOAD_ASYNC) {
            $attributes['async'] = true;
        } elseif ($loadStrategy === self::LOAD_DEFER) {
            $attributes['defer'] = true;
        }

        // Add preload hint if requested
        if ($preload) {
            $this->addPreloadHint($src, 'script', $options['priority'] ?? self::PRIORITY_AUTO);
        }

        return $this->secureRenderer->renderTag(
            'script',
            $attributes,
        );
    }

    /**
     * Render style tag with inline content
     *
     * @param File $asset The asset file
     * @param array{attributes?: array<string, string>} $options Rendering options
     * @return string The rendered style tag
     */
    private function renderInlineStyle(File $asset, array $options = []): string
    {
        $content = $this->getAssetContent($asset);

        if ($content === '') {
            return '';
        }

        $attributes = $options['attributes'] ?? [];

        return $this->secureRenderer->renderTag(
            'style',
            $attributes,
            $content,
            false
        );
    }

    /**
     * Render external stylesheet using HeadTagManager
     *
     * @param File $asset The asset file
     * @param bool $preload Whether to add preload resource hint
     * @param array{priority?: string, attributes?: array<string, string>, position?: string} $options Rendering options
     * @return string The rendered <link> tag
     */
    private function renderExternalStyle(
        File $asset,
        bool $preload,
        array $options = []
    ): string {
        $href = $asset->getUrl();
        $attributes = array_merge([
            'rel' => 'stylesheet',
            'type' => 'text/css'
        ], $options['attributes'] ?? []);

        // Assigned after the caller's attributes, exactly as the external-script path assigns `src`: the
        // address is the one thing the tag is useless without, so a caller must not be able to drop or
        // override it by passing an `href` of its own.
        $attributes['href'] = $href;

        // Add preload hint if requested
        if ($preload) {
            $this->addPreloadHint($href, 'style', $options['priority'] ?? self::PRIORITY_AUTO);
        }

        return $this->secureRenderer->renderTag(
            'link',
            $attributes
        );
    }

    /**
     * Get asset content using strategy pool
     *
     * @param File $asset The asset file to read
     * @return string The file content or empty string if unavailable
     */
    private function getAssetContent(File $asset): string
    {
        return $this->strategyPool->getContent($asset);
    }

    /**
     * Add preload resource hint using HeadTagManager
     *
     * @param string $href Resource URL
     * @param string $as Resource type (script, style, etc.)
     * @param string $priority Fetch priority
     * @return void
     */
    private function addPreloadHint(string $href, string $as, string $priority): void
    {
        $attributes = [
            'rel' => 'preload',
            'href' => $href,
            'as' => $as
        ];

        if ($priority !== self::PRIORITY_AUTO) {
            $attributes['fetchpriority'] = $priority;
        }

        $key = 'preload_' . md5($href . $as);
        $this->headTagManager->addLink($attributes, $key);
    }
}
