<?php

namespace App\Libraries;

use App\Models\FileUploadJobModel;
use App\Models\FileUploadJobLogModel;
use Exception;

class FileUploadJob {
    public static function getLastJob(string $jobName) {
        try {
            return FileUploadJobModel::where('job_name', $jobName)->latest('id')->first();
        } catch(Exception $e) {
            return false;
        }
    }

    public static function getJob(string $jobName) {
        try {
            return FileUploadJobModel::where('job_name', $jobName)->latest('id')->limit(5)->get();
        } catch(Exception $e) {
            return false;
        }
    }

    public static function createJob(string $jobName) {
        try {
            $job = new FileUploadJobModel();
            $job->job_name = $jobName;
            $job->save();
            return $job;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function updateJob(int $jobId, array $data) {
        try {
            return FileUploadJobModel::where('id', $jobId)->update($data);
        } catch(Exception $e) {
            return false;
        }
    }

    public static function getJobLog(int $jobId, string $status=null) {
        try {
            $logs = FileUploadJobLogModel::where('job_id', $jobId);
            if($status) {
                $logs = $logs->where('status', $status);
            }
            return $logs->latest('id')
                ->get();
        } catch(Exception $e) {
            return false;
        }
    }

    public static function createJobLog(array $data) {
        try {
            return FileUploadJobLogModel::create($data);
        } catch(Exception $e) {
            return false;
        }
    }
}
