<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Hryvinskyi\ThemeAssets\Api\AssetPathResolverInterface;

/**
 * ViewModel for resolving asset file paths and content
 */
class AssetPathResolver implements ArgumentInterface, AssetPathResolverInterface
{
    /**
     * @var array<string, File> $assetCache
     */
    private array $assetCache = [];

    /**
     * @param Repository $assetRepository
     */
    public function __construct(
        private readonly Repository $assetRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getViewFilePath(string $fileId, array $params = []): string
    {
        return $this->createAsset($fileId, $params)->getPath();
    }

    /**
     * @inheritDoc
     */
    public function getViewFileContent(string $fileId, array $params = []): string|false
    {
        return $this->createAsset($fileId, $params)->getContent();
    }

    /**
     * Create and cache asset by file ID
     *
     * @param string $fileId The file identifier
     * @param array{_secure?: bool, area?: string, theme?: string, locale?: string, module?: string} $params Asset parameters
     * @return File The created asset
     * @throws LocalizedException
     */
    public function createAsset(string $fileId, array $params = []): File
    {
        $cacheKey = $fileId . serialize($params);

        if (!isset($this->assetCache[$cacheKey])) {
            $this->assetCache[$cacheKey] = $this->assetRepository->createAsset($fileId, $params);
        }

        return $this->assetCache[$cacheKey];
    }
}
