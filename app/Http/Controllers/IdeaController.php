<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IdeaController extends Controller
{

    private array $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:255',
    ];

    private $errorMessage = [
        'title.required' => 'El campo titulo es obligatorio',
        'description.required' => 'El campo descripcion es obligatorio',
        'string' => 'Este campo debe ser de tipo String',
        'title.max' => 'El campo titulo no debe ser mayor a :max caracteres.',
        'descripcion.max' => 'El campo descripcion no debe ser mayor a :max caracteres.',
    ];
    
    public function index(Request $request):View {

        $ideas = Idea::myIdeas($request->filters)->theBest($request->filters)->get();

        
        return view('ideas.index',['ideas' => $ideas]);
    }

    
    public function create():View {
        return view('ideas.create_or_edit');
    }

    public function store(Request $request): RedirectResponse {
        $validated = $request->validate($this->rules, $this->errorMessage);

        Idea::create([
            'user_id' => auth()->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
        ]);

        session()->flash('message', 'Idea creada correctamente');

        return redirect()->route('ideas.index'); 
        
    }   

    public function edit(Idea $idea): View {
        $this->authorize('update', $idea);
        
        return view('ideas.create_or_edit')->with('idea', $idea);
    }
    
    public function update(Request $request, Idea $idea): RedirectResponse {
        $this->authorize('update', $idea);
        
        $validated = $request->validate($this->rules, $this->errorMessage);

        $idea->update($validated);

        session()->flash('message', 'Idea Actualizada correctamente');

        return redirect(route('ideas.index'));
    }
    
    public function show(Idea $idea): View {
        return view('ideas.show')->with('idea', $idea);
    }

    
    public function delete(Idea $idea): RedirectResponse {
        $this->authorize('delete', $idea);
        
        $idea->delete();

        session()->flash('message', 'Idea Eliminada correctamente');
        
        return redirect()->route('ideas.index');
    }

    public function synchronizeLikes(Request $request, Idea $idea): RedirectResponse {

        $this->authorize('updateLikes', $idea);
        $request->user()->ideasLiked()->toggle([$idea->id]);

        $idea->update(['likes'=> $idea->users()->count()]);

        return redirect()->route('ideas.show', $idea);
    }
    
}
