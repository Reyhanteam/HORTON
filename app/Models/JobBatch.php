<?php

namespace App\Models;

class JobBatch extends HortonModel
{
    protected $table = 'job_batches';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $casts = ['total_jobs' => 'integer', 'pending_jobs' => 'integer', 'failed_jobs' => 'integer', 'failed_job_ids' => 'array', 'cancelled_at' => 'datetime', 'created_at' => 'datetime', 'finished_at' => 'datetime'];
}
