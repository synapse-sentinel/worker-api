<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssistantRequest;
use App\Http\Requests\UpdateAssistantRequest;
use App\Models\Assistant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Response;

class AssistantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): LengthAwarePaginator
    {
        return Assistant::query()->paginate();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssistantRequest $request): Assistant
    {
        return Assistant::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Assistant $assistant): Assistant
    {
        return $assistant;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAssistantRequest $request, Assistant $assistant): Assistant
    {
        $assistant->update($request->validated());
        return $assistant;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assistant $assistant): Response
    {
        $assistant->delete();
        return response('Deleted', 204);
    }
}
