<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(): Collection|array
    {
        return Event::with('users')->get();
    }

    public function show($id): Model|Collection|Builder|array|null
    {
        return Event::with('users')->findOrFail($id);
    }

    public function store(Request $request): JsonResponse
    {
        $event = Event::create($request->all());

        if ($request->has('user_ids')) {
            $event->users()->attach($request->input('user_ids'));
        }

        return response()->json($event, 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());

        if ($request->has('user_ids')) {
            $event->users()->sync($request->input('user_ids'));
        }

        return response()->json($event);
    }

    public function destroy($id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Evento excluído com sucesso!'
        ], 204);
    }
}
