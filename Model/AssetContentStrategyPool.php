<?php
/**
 * Copyright (c) 2026. Volodymyr Hryvinskyi. All rights reserved.
 * Author: Volodymyr Hryvinskyi <volodymyr@hryvinskyi.com>
 * GitHub: https://github.com/hryvinskyi
 */

declare(strict_types=1);

namespace Hryvinskyi\ThemeAssets\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\File;
use Magento\Store\Model\StoreManagerInterface;
use Hryvinskyi\ThemeAssets\Api\AssetContentStrategyInterface;
use Hryvinskyi\ThemeAssets\Api\AssetContentStrategyPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * Pool of asset content retrieval strategies with proper dependency injection
 */
class AssetContentStrategyPool implements AssetContentStrategyPoolInterface
{
    /**
     * @var AssetContentStrategyInterface[]
     */
    private readonly array $strategies;

    /**
     * @param AssetContentStrategyInterface[] $strategies Array of strategies in priority order
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        array $strategies,
        private readonly LoggerInterface $logger,
        private readonly RequestInterface $request,
        private readonly StoreManagerInterface $storeManager
    ) {
        $this->strategies = $strategies;
    }

    /**
     * @inheritDoc
     */
    public function getContent(File $asset): string
    {
        foreach ($this->getStrategies() as $strategy) {
            try {
                $content = $strategy->getContent($asset);

                if ($content !== '' && $content !== false) {
                    return $content;
                }
            } catch (\Exception $e) {
                $this->logger->info("Asset content strategy '{$strategy->getName()}' failed", [
                    'file_path' => $asset->getPath(),
                    'file_url' => $asset->getUrl(),
                    'exception' => $e->getMessage(),
                    'strategy' => $strategy->getName()
                ]);
            }
        }

        $this->logger->info('All asset content strategies failed', [
            'file_path' => $asset->getPath(),
            'file_url' => $asset->getUrl(),
            'request' => $this->request->getFullActionName(),
            'url' => $this->storeManager->getStore()->getCurrentUrl(),
        ]);

        return '';
    }

    /**
     * @inheritDoc
     */
    public function getStrategies(): array
    {
        foreach ($this->strategies as $strategy) {
            if (!$strategy instanceof AssetContentStrategyInterface) {
                throw new \InvalidArgumentException(get_class($strategy) . " must implement AssetContentStrategyInterface");
            }
        }

        return $this->strategies;
    }
}
