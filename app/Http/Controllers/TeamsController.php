<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class TeamsController extends Controller
{   
    // get team list
    public function index(Request $request): JsonResponse {
        $request->validate([
            'count' => ['nullable','numeric','min:1','max:20'],
            'skip' => ['nullable','numeric','min:0'],
        ]);

        $count = 10;
        $skip = 0;
        if($request->has('skip')) {
            $skip = $request->skip;
        }
        if($request->has('count')) {
            $count = $request->count;
        }

        $res = Team::skip($skip)->take($count)->get();

        return response()->json(['workers' => $res, 'count' => Team::count()]);
    }

    public function show(Request $request, int $id): JsonResponse {
        //
        $team_id = 0;
        if($id) {
            $team_id = intval($id);
        }
        $res = Team::findOr($team_id, function () {
            return null;
        });
        //dd($res);
        if(!$res) {
            return response()->json(['id' => 'Not found'], 422);
        }

        return response()->json($res);
    }

    public function store(Request $request): JsonResponse {
        //
        $request->validate([
            'name' => ['nullable','string','max:255'],
            'position' => ['nullable','string','max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // до 5MB
        ]);

        $worker = new Team();

        $this->createOrEditWorker($worker, $request);

        return response()->json(['status' => 'Team\'s worker was created']);
    }

    public function edit(Request $request, int $id): JsonResponse {
        //
        $request->validate([
            'name' => ['nullable','string','max:255'],
            'position' => ['nullable','string','max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // до 5MB
        ]);

        $team_id = 0;
        if($id) {
            $team_id = intval($id);
        }
        // find worker by id
        $worker = Team::findOr($team_id, function () {
            return null;
        });
        
        if(!$worker) {
            return response()->json(['id' => 'Not found'], 422);
        }

        $this->createOrEditWorker($worker, $request);

        return response()->json(['status' => 'Team\'s worker was changed']);
    }

    public function delete(Request $request, int $id): JsonResponse {
        //
        $team_id = 0;
        if($id) {
            $team_id = intval($id);
        }
        // find worker by id
        $worker = Team::find($team_id);
        if(!$worker) {
            return response()->json(['id' => 'Not found'], 422);
        }
        // delete image if exist
        if($worker && $worker->imageHref && Storage::disk('public')->exists($worker->imageHref)) {
            Storage::disk('public')->delete($worker->imageHref);
        }

        $worker->delete();

        return response()->json(['status' => 'Team\'s worker was deleted']);
    }

    private function createOrEditWorker(Team $worker, Request $request) {
        if($request->has('name')) {
            $worker->name = $request->name;
        }
        if($request->has('position')) {
            $worker->position = $request->position;
        }
        if($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('images', 'public');
            // delete image if exist
            if($worker && $worker->imageHref && Storage::disk('public')->exists($worker->imageHref)) {
                Storage::disk('public')->delete($worker->imageHref);
            }
            $worker->imageHref = $path; // need add 'storage/' in frontend
        }
        if($worker->isDirty()) {
            $worker->save();
        }
    }
}
