<?php

namespace App\Libraries;

use App\Http\Controllers\Utilities;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Logger
{
    public static function write(string $fileName, array $payload)
    {
        try {
            // Write log to file
            if (!empty(dirname($fileName)) && !is_dir(dirname($fileName))) {
                @mkdir(dirname($fileName), 0777, true);
            }
            $file = fopen($fileName, "a");
            fwrite($file, ($payload['timestamp'] ?? date('Y-m-d H:i:s')) . ' : ' . ($payload['title'] ?? 'Data') . ' : ' . (json_encode($payload['data'],JSON_UNESCAPED_SLASHES) ?? "--") . PHP_EOL);
            fclose($file);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function archiveLogs(string $archiveFileDir)
    {
        try {
            // Write log to file
            if (!empty(dirname($archiveFileDir)) && !is_dir(dirname($archiveFileDir))) {
                @mkdir(dirname($archiveFileDir), 0777, true);
            }
            // Get last week all dates
            $firstDayOfLastWeek = date('Y-m-d', strtotime("last week"));
            $firstDayOfThisWeek = date('Y-m-d', strtotime("this week"));
            $day = $firstDayOfLastWeek;
            $week = [];
            while ($day < $firstDayOfThisWeek) {
                $week[] = $day;
                $day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
            }
            // Create zip file of all last week log files
            $logFiles = self::getFiles('logs', $week);

            // dd($week, $logFiles);

            // Create zip file
            $archiveFileName = trim($archiveFileDir, '/') . DIRECTORY_SEPARATOR . 'logs-from-' . $week[0] . '-to-' . $week[count($week) - 1] . '.zip';
            self::createArchive($archiveFileName, $logFiles);

            // Delete files from disk
            foreach ($logFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Move 30 days old data to s3 bucket
            self::uploadArchivedFiles($archiveFileDir);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function archiveBarcodes(string $archiveFileDir)
    {
        try {
            // Write log to file
            if (!empty(dirname($archiveFileDir)) && !is_dir(dirname($archiveFileDir))) {
                @mkdir(dirname($archiveFileDir), 0777, true);
            }
            // Get last week all dates
            $firstDayOfLastWeek = date('Y-m-d', strtotime("last week"));
            $firstDayOfThisWeek = date('Y-m-d', strtotime("this week"));
            $day = $firstDayOfLastWeek;
            $week = [];
            while ($day < $firstDayOfThisWeek) {
                $week[] = $day;
                $day = date('Y-m-d', strtotime('+1 day', strtotime($day)));
            }
            // Create zip file of all last week log files
            $logFiles = self::getFiles('public/assets/seller/images/barcodes/', $week);

            // dd($week, $logFiles);

            // Create zip file
            $archiveFileName = trim($archiveFileDir, '/') . DIRECTORY_SEPARATOR . 'barcodes-from-' . $week[0] . '-to-' . $week[count($week) - 1] . '.zip';
            self::createArchive($archiveFileName, $logFiles);

            // Move 30 days old data to s3 bucket
            self::uploadArchivedFiles($archiveFileDir);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function getFiles($logDir, $week, $logFiles = [])
    {
        try {
            foreach (array_diff(scandir($logDir), ['.', '..']) as $file) {
                $path = trim($logDir, '/') . DIRECTORY_SEPARATOR . $file;
                foreach ($week as $date) {
                    if (is_file($path)) {
                        if (date('Y-m-d', stat($path)['mtime']) == $date) {
                            $logFiles[] = $path;
                        }
                    } else {
                        $logFiles = self::getFiles($path, $week, $logFiles);
                    }
                }
            }
            return $logFiles;
        } catch (Exception $e) {
            return [];
        }
    }

    private static function uploadArchivedFiles($archiveFileDir)
    {
        try {
            $files = self::getArchivedFiles($archiveFileDir);
            foreach($files as $file) {
                $path = $file;
                $fileName = basename($file);
                $dir = explode('-', $fileName)[0] ?? 'other';
                if(!Storage::disk('s3')->exists("archive/{$dir}/{$fileName}") && Storage::disk('s3')->putFileAs("archive/{$dir}", new File($path), $fileName)) {
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }

    }

    private static function getArchivedFiles($archiveFileDir, $logFiles = [])
    {
        try {
            $date = date('Y-m-d', strtotime("-30 day"));
            foreach (array_diff(scandir($archiveFileDir), ['.', '..']) as $file) {
                $path = trim($archiveFileDir, '/') . DIRECTORY_SEPARATOR . $file;
                if (is_file($path)) {
                    if (date('Y-m-d', stat($path)['mtime']) <= $date) {
                        $logFiles[] = $path;
                    }
                } else {
                    $logFiles = self::getFiles($path, $logFiles);
                }
            }
            return $logFiles;
        } catch (Exception $e) {
            return [];
        }
    }

    private static function createArchive(string $archiveFileName, array $logFiles)
    {
        try {
            // Create zip file
            if (!empty($logFiles)) {
                $zip = new ZipArchive();
                if ($zip->open($archiveFileName, ZipArchive::CREATE) === TRUE) {
                    // Add files to archive
                    foreach ($logFiles as $file) {
                        $zip->addFile($file);
                    }
                    // All files are added, so close the zip file.
                    $zip->close();
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Cron job log
     *
     * @return mixed
     */
    public static function cronLog(
        $cron_name,
        $status,
        $remark,
        $success,
        $errors,
        $row_inserted,
        $row_updated,
        $row_deleted,
        $started_at,
        $finished_at = null
    ) {
        try {
            DB::insert('insert into cron_logs(cron_name, status, remark, success, errors, row_inserted, row_updated, row_deleted, started_at, finished_at, date) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $cron_name,
                $status,
                $remark,
                $success,
                $errors,
                $row_inserted,
                $row_updated,
                $row_deleted,
                $started_at,
                $finished_at,
                date('Y-m-d H:i:s')
            ]);
            DB::insert('insert into cron_jobs(job_name, started_at, finished_at, last_status, updated_at) values(?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE started_at = VALUES(started_at), finished_at = VALUES(finished_at), last_status = VALUES(last_status), updated_at = VALUES(updated_at)', [
                $cron_name,
                $started_at,
                $finished_at,
                $status,
                date('Y-m-d H:i:s')
            ]);

            if($status == "failed")
            {
                if($errors != "Time limit exceeded")
                {
                    $utilities = new Utilities();
                    $utilities->cronJobFailedStatus($cron_name,$status,$success,$errors,$started_at,$finished_at);
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function manualArchiveLogs($archiveFileName,$logFiles,$archiveFileDir){
        if(!empty($logFiles)) {
            try {
                self::createArchive($archiveFileName, $logFiles);
                foreach ($logFiles as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                // Move 30 days old data to s3 bucket
                self::uploadArchivedFiles($archiveFileDir);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
        else
            return true;
    }
}
