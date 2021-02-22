<?php

namespace Laravel\Horizon\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchedulerFileController extends Controller
{
    protected $schedulerFile = 'scheduler.json';
    /**
     * Get the scheduler.json file from the root of the main project.
     *
     * @return string|null
     */
    public function index()
    {
        if (! Storage::disk('local')->exists($this->schedulerFile)) {
            return response(null, 200);
        }

        return response(json_decode(Storage::disk('local')->get($this->schedulerFile), true), 200);
    }

    /**
     * Put the new scheduler to the scheduler.json file.
     *
     * @return string
     */
    public function store(Request $request)
    {
        $scheduler = $request->input('scheduler');
        $file = Storage::disk('local')->put($this->schedulerFile, json_encode($scheduler));

        if (!$file) {
            return  response('Can\'t write to file', 500);

        }

        return  $this->index();
    }
}
