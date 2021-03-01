<?php

namespace Laravel\Horizon\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class SchedulerFileController extends Controller
{
    protected $schedulerFile = 'scheduler.json';

    /**
     * Get the scheduler.json file from the root of the main project.
     *
     * @return Response
     */
    public function index()
    {
        if (! Storage::disk('local')->exists($this->schedulerFile)) {
            return response(null, 200);
        }

        $jobs = json_decode(Storage::disk('local')->get($this->schedulerFile), true);

        return response($jobs, 200);
    }

    /**
     * Update the scheduler.json.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $scheduler = $request->input('scheduler');
        $isFileUpdated = Storage::disk('local')->put($this->schedulerFile, json_encode($scheduler));

        if (!$isFileUpdated) {
            return response('Can\'t write to file', 500);
        }

        return $this->index();
    }
}
