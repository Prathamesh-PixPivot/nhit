<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PerformanceMonitoringService
{
    /**
     * Monitor application performance
     */
    public function monitorPerformance()
    {
        $metrics = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
            'database_queries' => $this->getDatabaseMetrics(),
            'cache_hits' => $this->getCacheMetrics(),
            'redis_metrics' => $this->getRedisMetrics(),
            'server_load' => $this->getServerLoad(),
        ];

        $this->logMetrics($metrics);
        $this->storeMetrics($metrics);

        return $metrics;
    }

    /**
     * Get database performance metrics
     */
    private function getDatabaseMetrics()
    {
        $queries = DB::getQueryLog();
        $totalQueries = count($queries);
        $slowQueries = array_filter($queries, function ($query) {
            return $query['time'] > 1000; // Queries taking more than 1 second
        });

        return [
            'total_queries' => $totalQueries,
            'slow_queries' => count($slowQueries),
            'average_time' => $totalQueries > 0 ? array_sum(array_column($queries, 'time')) / $totalQueries : 0,
            'max_time' => $totalQueries > 0 ? max(array_column($queries, 'time')) : 0,
        ];
    }

    /**
     * Get cache performance metrics
     */
    private function getCacheMetrics()
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'hits' => $info['keyspace_hits'] ?? 0,
                'misses' => $info['keyspace_misses'] ?? 0,
                'hit_rate' => $this->calculateHitRate($info),
                'memory_used' => $info['used_memory'] ?? 0,
                'connected_clients' => $info['connected_clients'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Redis metrics error: ' . $e->getMessage());
            return [
                'hits' => 0,
                'misses' => 0,
                'hit_rate' => 0,
                'memory_used' => 0,
                'connected_clients' => 0,
            ];
        }
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate($info)
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? ($hits / $total) * 100 : 0;
    }

    /**
     * Get Redis metrics
     */
    private function getRedisMetrics()
    {
        try {
            $redis = Redis::connection();
            $info = $redis->info();
            
            return [
                'version' => $info['redis_version'] ?? 'Unknown',
                'uptime' => $info['uptime_in_seconds'] ?? 0,
                'memory_used' => $info['used_memory'] ?? 0,
                'memory_peak' => $info['used_memory_peak'] ?? 0,
                'connected_clients' => $info['connected_clients'] ?? 0,
                'total_commands' => $info['total_commands_processed'] ?? 0,
                'keyspace' => $info['db0'] ?? 'No keyspace',
            ];
        } catch (\Exception $e) {
            Log::error('Redis connection error: ' . $e->getMessage());
            return [
                'version' => 'Unknown',
                'uptime' => 0,
                'memory_used' => 0,
                'memory_peak' => 0,
                'connected_clients' => 0,
                'total_commands' => 0,
                'keyspace' => 'No keyspace',
            ];
        }
    }

    /**
     * Get server load metrics
     */
    private function getServerLoad()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0] ?? 0,
                '5min' => $load[1] ?? 0,
                '15min' => $load[2] ?? 0,
            ];
        }

        return [
            '1min' => 0,
            '5min' => 0,
            '15min' => 0,
        ];
    }

    /**
     * Log performance metrics
     */
    private function logMetrics($metrics)
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'memory_usage_mb' => round($metrics['memory_usage'] / 1024 / 1024, 2),
            'peak_memory_mb' => round($metrics['peak_memory'] / 1024 / 1024, 2),
            'execution_time_ms' => round($metrics['execution_time'] * 1000, 2),
            'database_queries' => $metrics['database_queries']['total_queries'],
            'slow_queries' => $metrics['database_queries']['slow_queries'],
            'cache_hit_rate' => round($metrics['cache_hits']['hit_rate'], 2),
            'server_load' => $metrics['server_load']['1min'],
        ];

        Log::info('Performance Metrics', $logData);
    }

    /**
     * Store metrics in cache for monitoring
     */
    private function storeMetrics($metrics)
    {
        $key = 'performance_metrics_' . now()->format('Y-m-d-H-i');
        
        Cache::put($key, $metrics, 3600); // Store for 1 hour
        
        // Store daily summary
        $dailyKey = 'daily_metrics_' . now()->format('Y-m-d');
        $dailyMetrics = Cache::get($dailyKey, []);
        $dailyMetrics[] = $metrics;
        Cache::put($dailyKey, $dailyMetrics, 86400); // Store for 24 hours
    }

    /**
     * Get performance alerts
     */
    public function getPerformanceAlerts()
    {
        $alerts = [];
        
        // Check memory usage
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        if ($memoryUsage > 256) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "High memory usage: {$memoryUsage}MB",
                'timestamp' => now(),
            ];
        }

        // Check execution time
        $executionTime = microtime(true) - LARAVEL_START;
        if ($executionTime > 5) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Slow execution time: {$executionTime}s",
                'timestamp' => now(),
            ];
        }

        // Check database queries
        $queries = DB::getQueryLog();
        if (count($queries) > 100) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "High number of database queries: " . count($queries),
                'timestamp' => now(),
            ];
        }

        return $alerts;
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport($date = null)
    {
        $date = $date ?: now()->format('Y-m-d');
        $dailyKey = 'daily_metrics_' . $date;
        $metrics = Cache::get($dailyKey, []);

        if (empty($metrics)) {
            return [
                'date' => $date,
                'message' => 'No metrics available for this date',
                'metrics' => [],
            ];
        }

        $report = [
            'date' => $date,
            'total_requests' => count($metrics),
            'average_memory_usage' => $this->calculateAverage($metrics, 'memory_usage'),
            'peak_memory_usage' => $this->calculateMax($metrics, 'peak_memory'),
            'average_execution_time' => $this->calculateAverage($metrics, 'execution_time'),
            'max_execution_time' => $this->calculateMax($metrics, 'execution_time'),
            'total_database_queries' => $this->calculateSum($metrics, 'database_queries.total_queries'),
            'slow_queries' => $this->calculateSum($metrics, 'database_queries.slow_queries'),
            'average_cache_hit_rate' => $this->calculateAverage($metrics, 'cache_hits.hit_rate'),
            'average_server_load' => $this->calculateAverage($metrics, 'server_load.1min'),
        ];

        return $report;
    }

    /**
     * Calculate average value from metrics array
     */
    private function calculateAverage($metrics, $key)
    {
        $values = array_column($metrics, $key);
        return count($values) > 0 ? round(array_sum($values) / count($values), 2) : 0;
    }

    /**
     * Calculate maximum value from metrics array
     */
    private function calculateMax($metrics, $key)
    {
        $values = array_column($metrics, $key);
        return count($values) > 0 ? round(max($values), 2) : 0;
    }

    /**
     * Calculate sum from metrics array
     */
    private function calculateSum($metrics, $key)
    {
        $values = array_column($metrics, $key);
        return count($values) > 0 ? array_sum($values) : 0;
    }
}
