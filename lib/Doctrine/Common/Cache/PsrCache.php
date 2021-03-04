<?php

namespace Doctrine\Common\Cache;

use Psr\Cache\CacheItemPoolInterface;

use function rawurlencode;
use function time;

class PsrCache extends CacheProvider
{
    /** @var int */
    private $hitsCount = 0;

    /** @var int */
    private $missesCount = 0;

    /** @var int */
    private $upTime;

    /** @var CacheItemPoolInterface */
    private $pool;

    public function __construct(CacheItemPoolInterface $pool)
    {
        $this->pool   = $pool;
        $this->upTime = time();
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $item = $this->pool->getItem(rawurlencode($id));

        if (! $item->isHit()) {
            ++$this->missesCount;

            return false;
        }

        ++$this->hitsCount;

        return $item->get();
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id): bool
    {
        return $this->pool->hasItem(rawurlencode($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0): bool
    {
        $item = $this->pool->getItem(rawurlencode($id));

        if (0 < $lifeTime) {
            $item->expiresAfter($lifeTime);
        }

        return $this->pool->save($item->set($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id): bool
    {
        return $this->pool->deleteItem(rawurlencode($id));
    }

    protected function doFlush(): bool
    {
        return $this->pool->clear();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats(): ?array
    {
        return [
            Cache::STATS_HITS             => $this->hitsCount,
            Cache::STATS_MISSES           => $this->missesCount,
            Cache::STATS_UPTIME           => $this->upTime,
            Cache::STATS_MEMORY_USAGE     => null,
            Cache::STATS_MEMORY_AVAILABLE => null,
        ];
    }
}
