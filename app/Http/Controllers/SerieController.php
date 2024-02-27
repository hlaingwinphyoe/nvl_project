<?php

namespace App\Http\Controllers;

use App\Http\Requests\SerieStoreRequest;
use App\Models\Serie;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SerieController extends Controller
{
    public function index()
    {
        $perPage = request('page_size') ?: 10;
        $series = Serie::query()
            ->filterOn()
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn ($serie) => [
                'id' => $serie->id,
                'title' => $serie->title,
                'description' => $serie->description,
                'price' => $serie->price,
                'user' => $serie->owner,
                'is_published' => $serie->is_published,
                'created_at' => $serie->created_at->diffforHumans()
            ]);

        return Inertia::render('Admin/Series/Index', [
            'series' => $series
        ]);
    }

    public function store(SerieStoreRequest $request)
    {
        $serie = Serie::create([
            'slug' => Str::slug($request->title),
            'title' => $request->title,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Successfully Created.');
    }

    public function update(SerieStoreRequest $request, $id)
    {
        $serie = Serie::findOrFail($id);
        $serie->update([
            'slug' => Str::slug($request->title),
            'title' => $request->title,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy()
    {
        abort(404);
    }

    public function changeStatus(Serie $serie)
    {
        if ($serie->is_published == 0) {
            $serie->update(['is_published' => 1]);
        } else {
            $serie->update(['is_published' => 0]);
        }
        return redirect()->back()->with('success', 'Successfully Changed.');
    }
}
