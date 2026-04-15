<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UsersDataTableController extends Controller
{
    public function __invoke(Request $request)
    {
        $users = User::query()
            ->with('meta')
            ->whereNotIn('role', ['admin', 'superAdmin'])
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($request->college, function ($query, $college) {
                $query->where('college', $college);
            })
            ->when($request->department, function ($query, $department) {
                $query->where('department', $department);
            });

        return DataTables::of($users)
            ->addColumn('user', function ($user) {
                $initials = method_exists($user, 'initials') ? $user->initials() : substr($user->name, 0, 2);

                return view('livewire.admin.users.partials.user-cell', ['user' => $user, 'initials' => $initials])->render();
            })
            ->addColumn('college', function ($user) {
                return $user->college?->label() ?? 'N/A';
            })
            ->addColumn('department', function ($user) {
                return $user->department?->label() ?? 'N/A';
            })
            ->addColumn('role', function ($user) {
                return $user->role?->label() ?? 'N/A';
            })
            ->rawColumns(['user'])
            ->toJson();
    }
}
