<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\QueueService;

class QueueController extends Controller
{
    protected $queueService;

    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
    }

    public function live()
    {
        $queue = $this->queueService->getTodayQueue();
        return view('queue.live', compact('queue'));
    }

    public function manage()
    {
        $queue = $this->queueService->getTodayQueue();
        return view('queue.live', compact('queue'));
    }
}
