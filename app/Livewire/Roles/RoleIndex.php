<?php

namespace App\Livewire\Roles;

use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Livewire\Component;

class RoleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate;
    public $endDate;


    public function clearFilters()
    {
        $this->search = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $query = Role::query();
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }
        // Apply search filter
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        $roles = $query->oldest()->paginate(5);

        $firstItem = ($roles->currentPage() - 1) * $roles->perPage() + 1;

        return view('livewire.roles.role-index', compact('roles', 'firstItem'));
    }

    public function delete($id)
    {
        $role = Role::find($id);
        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
        return to_route('roles.index');
    }
}
