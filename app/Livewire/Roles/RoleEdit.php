<?php

namespace App\Livewire\Roles;

use Spatie\Permission\Models\Permission;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleEdit extends Component
{
    public $name, $role, $allPermissions = [], $permissions = [];

    public function mount($id)
    {
        $this->role = Role::find($id);
        $this->name = $this->role->name;
        $this->permissions = $this->role->permissions->pluck('name');
        $this->allPermissions = Permission::all();
    }

    public function render()
    {
        return view('livewire.roles.role-edit');
    }
    public function submit()
    {
        $this->validate([
            'name' => 'required|unique:roles,name,' .  $this->role->id,
            'permissions' => 'required',
        ]);

        $this->role->name = $this->name;

        $this->role->save();
        $this->role->syncPermissions($this->permissions);

        return to_route('roles.index')->with('success', 'Role updated successfully.');
    }
}
