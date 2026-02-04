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
 * Interface for asset content retrieval strategies
 */
interface AssetContentStrategyInterface
{
    /**
     * Get asset content using specific strategy
     *
     * @param File $asset The asset file to read
     * @return string|false Content on success, false on failure
     * @throws \Exception If strategy encounters an error
     */
    public function getContent(File $asset): string|false;

    /**
     * Get strategy name for logging purposes
     *
     * @return string Strategy identifier
     */
    public function getName(): string;
}
