<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PsrCache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class PsrCacheTest extends CacheTest
{
    protected function getCacheDriver(): CacheProvider
    {
        return new PsrCache(
            new ArrayAdapter()
        );
    }

    protected function isSharedStorage(): bool
    {
        return false;
    }
}
