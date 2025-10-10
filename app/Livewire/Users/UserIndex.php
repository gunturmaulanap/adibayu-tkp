<?php

namespace App\Livewire\Users;

use App\Models\User;

use Livewire\Component;
use Livewire\WithPagination;


class UserIndex extends Component
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
        $query = User::query();
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        $users = $query->oldest()->paginate(5);

        $firstItem = ($users->currentPage() - 1) * $users->perPage() + 1;

        return view('livewire.users.user-index', compact('users', 'firstItem'));
    }
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();

        session()->flash('success', 'User deleted successfully.');
        return to_route('users.index');
    }
}
