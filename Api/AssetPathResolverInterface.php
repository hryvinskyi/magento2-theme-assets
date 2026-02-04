<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\Api;

use Magento\Framework\View\Asset\File;

/**
 * Interface for resolving asset file paths
 */
interface AssetPathResolverInterface
{
    /**
     * Get the file path for a view asset
     *
     * @param string $fileId The file identifier (e.g., 'images/logo.svg')
     * @param array{_secure?: bool, area?: string, theme?: string, locale?: string, module?: string} $params Asset parameters
     * @return string The resolved file path
     * @throws \Magento\Framework\Exception\LocalizedException If the asset cannot be resolved
     */
    public function getViewFilePath(string $fileId, array $params = []): string;

    /**
     * Get the file content for a view asset
     *
     * @param string $fileId The file identifier (e.g., 'images/logo.svg')
     * @param array{_secure?: bool, area?: string, theme?: string, locale?: string, module?: string} $params Asset parameters
     * @return string|false The file content or false if the file cannot be read
     * @throws \Magento\Framework\Exception\LocalizedException If the asset cannot be resolved
     */
    public function getViewFileContent(string $fileId, array $params = []): string|false;

    /**
     * Create and cache asset by file ID
     *
     * @param string $fileId The file identifier (e.g., 'images/logo.svg')
     * @param array{_secure?: bool, area?: string, theme?: string, locale?: string, module?: string} $params Asset parameters
     * @return File The created asset
     * @throws \Magento\Framework\Exception\LocalizedException If the asset cannot be resolved
     */
    public function createAsset(string $fileId, array $params = []): File;
}
