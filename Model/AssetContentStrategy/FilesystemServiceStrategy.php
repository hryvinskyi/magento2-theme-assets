<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\Model\AssetContentStrategy;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Asset\File;
use Hryvinskyi\ThemeAssets\Api\AssetContentStrategyInterface;

/**
 * Asset content retrieval strategy using filesystem service with absolute path
 */
class FilesystemServiceStrategy implements AssetContentStrategyInterface
{
    /**
     * @param Filesystem $filesystem
     */
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getContent(File $asset): string|false
    {
        $staticPath = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)->getAbsolutePath();
        $fullPath = $staticPath . $asset->getPath();

        if (file_exists($fullPath) && is_readable($fullPath)) {
            $content = file_get_contents($fullPath);
            return $content !== false ? $content : false;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'filesystem_service';
    }
}
