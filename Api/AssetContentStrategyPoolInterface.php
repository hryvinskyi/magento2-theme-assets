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
 * Pool of asset content retrieval strategies
 */
interface AssetContentStrategyPoolInterface
{
    /**
     * Get asset content using available strategies in order
     *
     * @param File $asset The asset file to read
     * @return string Content or empty string if all strategies fail
     */
    public function getContent(File $asset): string;

    /**
     * Get all available strategies
     *
     * @return AssetContentStrategyInterface[] Array of strategies
     */
    public function getStrategies(): array;
}
