<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        abort_if(Gate::denies('view-users'), Response::HTTP_FORBIDDEN, __('cms.authorization_error'));

        return Inertia::render('Users/Index', [
            'users' => User::query()
                ->with('roles')
                ->when(Request::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                    $query->orWhere('email', 'like', "%{$search}%");
                    $query->orWhereHas('roles', function ($subquery) use ($search) {
                        return $subquery->where('name', 'like',  "%{$search}%");
                    });
                })
                ->paginate(20)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'role' => $user->roles[0]->name ?? '',
                ]),
            'filters' => Request::only(['search'])
        ]);
    }
}
