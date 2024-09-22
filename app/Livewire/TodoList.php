<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $search;

    public $todoID;

    #[Rule('required|min:3|max:50')]
    public $newName;

    public function create(){
        $validated = $this->validateOnly('name');
        Todo::create($validated);
        $this->reset('name');
        session()->flash('success', 'Created.');
    }

    public function delete($todoID){
        try{
            Todo::findOrfail($todoID)->delete();
        }
        catch(Exception $e)
        {
            session()->flash('Error', 'Failed to delete todo');
            return;
        }

    }

    public function getTodo($todoID)
    {
        return Todo::find($todoID);
    }

    public function toggle($todoID)
    {
        $todo = Todo::find($todoID);
        $todo->completed = !$todo->completed;
        $todo->save();

        $this->resetPage();
    }

    public function edit($todoID){
        $this->todoID = $todoID;
        $this->newName = Todo::find($todoID)->name;
    }

    public function cancel(){
        $this->reset('todoID','newName');
    }

    public function update(){
        $this->validateOnly('newName');
        Todo::find($this->todoID)->update(
            [
                'name' => $this->newName
            ]
        );
    }

    public function render()
    {
        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name','like',"%{$this->search}%")->paginate(5)
         ]);
    }
}
