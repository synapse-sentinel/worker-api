<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ThreadCollection
    {
        return new ThreadCollection(Thread::query()->paginate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThreadRequest $request): ThreadResource
    {
        return new ThreadResource(Thread::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread): ThreadResource
    {
        return new ThreadResource($thread);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThreadRequest $request, Thread $thread): Thread
    {
        $thread->update($request->validated());

        return $thread;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread): JsonResponse
    {
        $thread->delete();

        return response()->json(null, 204);
    }
}
