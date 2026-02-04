<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\Model\AssetContentStrategy;

use Magento\Framework\View\Asset\File;
use Hryvinskyi\ThemeAssets\Api\AssetContentStrategyInterface;

/**
 * Asset content retrieval strategy using Magento's standard asset system
 */
class MagentoAssetSystemStrategy implements AssetContentStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function getContent(File $asset): string|false
    {
        try {
            return $asset->getContent();
        } catch (File\NotFoundException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'magento_asset_system';
    }
}
