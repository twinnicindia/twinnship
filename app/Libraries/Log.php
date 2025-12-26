<?php

namespace App\Libraries;

use Exception;
use Elastic\Elasticsearch\ClientBuilder;

class Log
{
    /**
     * All log levels
     * 
     * @var array
     */
    static private $logLevels = [
        'emergency' => 7,
        'alert' => 6,
        'critical' => 5,
        'error' => 4,
        'warning' => 3,
        'notice' => 2,
        'info' => 1,
        'debug' => 0,
    ];

    /**
     * Log file path
     * 
     * @var string
     */
    static private $filePath = null;

    /**
     * Log index name
     * 
     * @var string
     */
    static private $indexName = null;

    /**
     * Set custom log path
     * 
     * @param string $path
     * @return self
     */
    public static function useFile(string $path): self
    {
        self::$filePath = $path;
        $self = new self;
        return $self;
    }

    /**
     * Set custom index name
     * 
     * @param string $name
     * @return self
     */
    public static function useIndex(string $name): self
    {
        self::$indexName = $name;
        $self = new self;
        return $self;
    }

    public static function debug($message, $context = null)
    {
        self::write('debug', $message, $context);
    }

    public static function info($message, $context = null)
    {
        self::write('info', $message, $context);
    }

    public static function notice($message, $context = null)
    {
        self::write('notice', $message, $context);
    }

    public static function warning($message, $context = null)
    {
        self::write('warning', $message, $context);
    }

    public static function error($message, $context = null)
    {
        self::write('error', $message, $context);
    }

    public static function critical($message, $context = null)
    {
        self::write('critical', $message, $context);
    }

    public static function alert($message, $context = null)
    {
        self::write('alert', $message, $context);
    }

    public static function emergency($message, $context = null)
    {
        self::write('emergency', $message, $context);
    }

    /**
     * Write log to file
     * 
     * @param string $level
     * @param string $message
     * @param mixed $context
     * @return void
     */
    private static function write($level, $message, $context = null)
    {
        try {
            $logLevel = env('LOG_LEVEL', 'debug');
            $logType = env('LOG_FILE_TYPE', 'single');
            $logDriver = array_map('strtolower', array_map('trim', explode(',', env('LOG_DRIVER', 'file'))));
            $filePath = self::$filePath ?? storage_path('logs/logs.log');
            self::$filePath = null;
            $indexName = self::$indexName ?? 'logs';
            self::$indexName = null;

            // Check log level is writable or not
            if (strtolower(env('APP_ENV')) == 'production' && self::$logLevels[strtolower($level)] < self::$logLevels[strtolower($logLevel)]) {
                return;
            }

            // Get log file info
            $pathinfo = pathinfo($filePath);
            $fileName = $pathinfo['filename'] ?? 'logs';

            // Store logs in elasticsearch
            if (in_array('elasticsearch', $logDriver)) {
                $client = ClientBuilder::create()->build();
                $document = [
                    'index' => $indexName,
                    'body' => [
                        'timestamp' => now()->toISOString(),
                        'level' => strtolower(env('APP_ENV')) . '.' . strtoupper($level),
                        'message' => $message,
                        'payload' => ($context == null ? '' : json_encode($context) ?? '')
                    ],
                ];
                $client->index($document);
            }

            // Store logs in file
            if (in_array('file', $logDriver)) {
                // Create dir if not exists
                if (!empty($pathinfo['dirname']) && !is_dir($pathinfo['dirname'])) {
                    @mkdir($pathinfo['dirname'], 0777, true);
                }
                // Write log to file
                if (strtolower($logType) == 'single') {
                    $filePath = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $fileName . '.log';
                } else {
                    $filePath = $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $fileName . '-' . date('Y-m-d') . '.log';
                }
                $file = fopen($filePath, 'a');
                fwrite($file, '[' . now()->format('Y-m-d H:i:s') . '] ' . strtolower(env('APP_ENV')) . '.' . strtoupper($level) . ': ' . $message . ' ' . ($context == null ? '' : json_encode($context) ?? '') . PHP_EOL);
                fclose($file);
            }
        } catch (Exception $e) {
            return;
        }
    }
}
