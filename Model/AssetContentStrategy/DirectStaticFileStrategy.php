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
 * Asset content retrieval strategy using direct access to static file directory
 */
class DirectStaticFileStrategy implements AssetContentStrategyInterface
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
        $staticDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        $filePath = $asset->getPath();

        if ($staticDir->isFile($filePath)) {
            return $staticDir->readFile($filePath);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'direct_static_file';
    }
}
