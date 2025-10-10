<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserEdit extends Component
{
    public $user, $name, $email, $password, $password_confirmation, $allRoles;
    public $roles = [];


    public function render()
    {
        return view('livewire.users.user-edit');
    }
    public function mount($id)
    {
        $this->user = User::find($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->allRoles = Role::all();
        $this->roles = $this->user->roles->pluck('name')->toArray();
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'password' => 'same:password_confirmation',
            'roles' => 'required|array',
        ]);

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        if ($this->password) {
            $this->user->password = Hash::make($this->password);
        }
        $this->user->save();
        $this->user->syncRoles($this->roles);


        return to_route('users.index')->with('success', 'User updated successfully.');
    }
}
