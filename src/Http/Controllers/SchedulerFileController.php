<?php

namespace Laravel\Horizon\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SchedulerFileController extends Controller
{
    private const SCHEDULER_FILENAME = 'scheduler.json';

    /**
     * Get the scheduler.json file from the root of the main project.
     *
     * @return Response
     */
    public function index()
    {
        if (!Storage::disk('local')->exists(self::SCHEDULER_FILENAME)) {
            return response(null, 200);
        }

        $jobs = json_decode(Storage::disk('local')->get(self::SCHEDULER_FILENAME), true);

        return response($jobs, 200);
    }

    /**
     * Update the scheduler.json.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $scheduler = json_encode($request->input('scheduler'), JSON_PRETTY_PRINT);
        $isFileUpdated = Storage::disk('local')->put(self::SCHEDULER_FILENAME, $scheduler);

        if (!$isFileUpdated) {
            return response('Can\'t write to file', 500);
        }

        return $this->index();
    }
}
