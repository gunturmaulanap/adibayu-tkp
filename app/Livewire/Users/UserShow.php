<?php

namespace App\Livewire\Users;

use App\Models\User;

use Livewire\Component;

class UserShow extends Component
{
    public $user;
    public function render()
    {
        return view('livewire.users.user-show');
    }
    public function mount($id)
    {
        $this->user = User::find($id);
    }
}
