<?php

namespace App\Services;

use App\Models\SchoolExperimentCatalogConfig;
use App\Models\ExperimentCatalogCompletionBaseline;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SchoolCatalogConfigCacheService
{
    /**
     * 缓存键前缀
     */
    const CACHE_PREFIX = 'school_catalog_config:';
    const CACHE_TTL = 3600; // 1小时

    /**
     * 获取学校配置（带缓存）
     */
    public function getSchoolConfig(int $schoolId): ?SchoolExperimentCatalogConfig
    {
        $cacheKey = self::CACHE_PREFIX . "school:{$schoolId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($schoolId) {
            return SchoolExperimentCatalogConfig::getActiveConfig($schoolId);
        });
    }

    /**
     * 获取学校完成率统计（带缓存）
     */
    public function getSchoolCompletionStats(int $schoolId, array $filters = []): array
    {
        $filterKey = md5(json_encode($filters));
        $cacheKey = self::CACHE_PREFIX . "completion:{$schoolId}:{$filterKey}";
        
        return Cache::remember($cacheKey, 300, function() use ($schoolId, $filters) {
            return ExperimentCatalogCompletionBaseline::getSchoolOverallRate($schoolId);
        });
    }

    /**
     * 获取完成率排行榜（带缓存）
     */
    public function getCompletionRanking(array $schoolIds, array $filters = []): array
    {
        $schoolKey = md5(json_encode($schoolIds));
        $filterKey = md5(json_encode($filters));
        $cacheKey = self::CACHE_PREFIX . "ranking:{$schoolKey}:{$filterKey}";
        
        return Cache::remember($cacheKey, 600, function() use ($schoolIds, $filters) {
            return ExperimentCatalogCompletionBaseline::getCompletionRanking($schoolIds, $filters);
        });
    }

    /**
     * 获取可用组织列表（带缓存）
     */
    public function getAvailableOrganizations(int $schoolId, int $level): array
    {
        $cacheKey = self::CACHE_PREFIX . "orgs:{$schoolId}:{$level}";
        
        return Cache::remember($cacheKey, 7200, function() use ($schoolId, $level) {
            // 这里应该调用权限服务的方法
            $permissionService = app(SchoolExperimentCatalogPermissionService::class);
            $user = auth()->user();
            return $permissionService->getAvailableOrganizations($user, $schoolId, $level);
        });
    }

    /**
     * 清除学校相关缓存
     */
    public function clearSchoolCache(int $schoolId): void
    {
        $patterns = [
            self::CACHE_PREFIX . "school:{$schoolId}",
            self::CACHE_PREFIX . "completion:{$schoolId}:*",
            self::CACHE_PREFIX . "orgs:{$schoolId}:*"
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // 清除匹配模式的缓存
                $this->clearCacheByPattern($pattern);
            } else {
                Cache::forget($pattern);
            }
        }

        Log::info("Cleared cache for school {$schoolId}");
    }

    /**
     * 清除排行榜缓存
     */
    public function clearRankingCache(): void
    {
        $this->clearCacheByPattern(self::CACHE_PREFIX . "ranking:*");
        Log::info("Cleared ranking cache");
    }

    /**
     * 清除所有相关缓存
     */
    public function clearAllCache(): void
    {
        $this->clearCacheByPattern(self::CACHE_PREFIX . "*");
        Log::info("Cleared all school catalog config cache");
    }

    /**
     * 按模式清除缓存
     */
    private function clearCacheByPattern(string $pattern): void
    {
        try {
            // 如果使用Redis，可以使用KEYS命令
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // 对于其他缓存驱动，需要手动管理缓存键
                // 这里可以维护一个缓存键列表
                Log::warning("Pattern cache clearing not supported for current cache driver");
            }
        } catch (\Exception $e) {
            Log::error("Failed to clear cache by pattern: {$pattern}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 预热缓存
     */
    public function warmupCache(array $schoolIds): void
    {
        foreach ($schoolIds as $schoolId) {
            try {
                // 预热学校配置缓存
                $this->getSchoolConfig($schoolId);
                
                // 预热完成率统计缓存
                $this->getSchoolCompletionStats($schoolId);
                
                // 预热可用组织缓存
                for ($level = 1; $level <= 3; $level++) {
                    $this->getAvailableOrganizations($schoolId, $level);
                }
                
                Log::info("Warmed up cache for school {$schoolId}");
            } catch (\Exception $e) {
                Log::error("Failed to warm up cache for school {$schoolId}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * 获取缓存统计信息
     */
    public function getCacheStats(): array
    {
        $stats = [
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0
        ];

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $info = $redis->info('memory');
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'Unknown';
                
                $keyPattern = self::CACHE_PREFIX . "*";
                $keys = $redis->keys($keyPattern);
                $stats['total_keys'] = count($keys);
            }
        } catch (\Exception $e) {
            Log::error("Failed to get cache stats", [
                'error' => $e->getMessage()
            ]);
        }

        return $stats;
    }

    /**
     * 配置变更时的缓存更新策略
     */
    public function onConfigChanged(int $schoolId, array $oldConfig = null, array $newConfig = null): void
    {
        // 清除相关缓存
        $this->clearSchoolCache($schoolId);
        
        // 如果配置影响到排行榜，清除排行榜缓存
        if ($oldConfig && $newConfig) {
            $affectsRanking = $oldConfig['source_level'] !== $newConfig['source_level'] ||
                             $oldConfig['source_org_id'] !== $newConfig['source_org_id'];
            
            if ($affectsRanking) {
                $this->clearRankingCache();
            }
        }
        
        // 异步预热新配置的缓存
        dispatch(function() use ($schoolId) {
            $this->warmupCache([$schoolId]);
        })->afterResponse();
    }
}
