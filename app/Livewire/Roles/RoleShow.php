<?php

namespace App\Livewire\Roles;

use Spatie\Permission\Models\Role;

use Livewire\Component;

class RoleShow extends Component
{
    public $role;

    public function mount($id)
    {

        $this->role = Role::find($id);
    }

    public function render()
    {
        return view('livewire.roles.role-show');
    }
}
