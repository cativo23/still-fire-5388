<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Exception;
use Gate;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Silber\Bouncer\Database\Role;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|void
     */
    public function index()
    {
        $users = User::latest()->paginate(5);

        return view('users.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View|void
     */
    public function create()
    {
        return view('super.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required', 'alpha_num', 'max:191', 'unique:users'],
            'email' => ['required', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:10', 'max:30', 'password'],
            'roles' => ['required', 'array']
        ]);

        $request->replace([
            'password' => Hash::make($request->input('password')),
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'roles' => $request->input('roles'),
            'email' => $request->input('email'),
        ]);

        $user = User::create($request->all());

        foreach ($request->input('roles') as $role) {
            $user->assign($role);
        }

        return redirect()->route('super.users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Application|Factory|View|void
     */
    public function show(User $user)
    {
        if (!Gate::allows('manage-users')) {
            return abort(401);
        }

        $user->load('roles');

        $user_auth = Auth::user();

        list($sidebar, $header, $footer) = VoyargeHelper::instance()->GetDashboard($user_auth);

        return view('super.users.show', compact('user', 'sidebar', 'header', 'footer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|void
     */
    public function edit($id)
    {
        if (!Gate::allows('manage-users')) {
            return abort(401);
        }

        $roles = Role::get()->pluck('name', 'name');

        $user = User::findOrFail($id);

        $user_auth = Auth::user();

        list($sidebar, $header, $footer) = VoyargeHelper::instance()->GetDashboard($user_auth);

        return view('super.users.edit', compact('user', 'roles', 'sidebar', 'header', 'footer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response|void
     */
    public function update(Request $request, $id)
    {

        if (!Gate::allows('manage-users')) {
            return abort(401);
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required', 'alpha_num', 'max:191', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:10', 'max:30', new StrongPassword],
            'roles' => ['required', 'array']
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('super.users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->input('password'))
            $password = Hash::make($request->input('password'));
        else
            $password = $user->getAuthPassword();

        $user->NAME = $request->input('name');
        $user->USERNAME = $request->input('username');
        $user->EMAIL = $request->input('email');
        $user->PASSWORD = $password;

        $user->save();

        foreach ($user->roles as $role) {
            $user->retract($role);
        }
        foreach ($request->input('roles') as $role) {
            $user->assign($role);
        }

        return redirect()->route('super.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse|Response|void
     * @throws Exception
     */
    public function destroy($id)
    {
        if (!Gate::allows('manage-users')) {
            return abort(401);
        }
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('super.users.index');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     * @return Response|void
     */
    public function mass(Request $request)
    {
        if (!Gate::allows('manage-users')) {
            return abort(401);
        }
        User::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }

    public function ban($request, $id)
    {
        if (!Gate::allows('manage-users') && !Gate::allows('ban-user')) {
            return abort(401);
        }

        $user = User::where('id', '=', $id);

        $user->ban([
            'expired_at' => '+1 month',
            'comment' => 'Prueba de ban'
        ]);

        return response()->redirectToRoute(route('super.users.index'));
    }

    public function showGiveAirportPermission(User $user)
    {
        $user_auth = Auth::user();
        list($sidebar, $header, $footer) = VoyargeHelper::instance()->GetDashboard($user_auth);
        return view('super.users.give_airport', compact('user', 'sidebar', 'header', 'footer'));
    }

    public function giveAirportPermission(Request $request, User $user)
    {
        $request->validate([
            'airport_id' => 'required|integer|exists:airports,id'
        ]);
        if ($user->can('manage-airport')) {
            $airport = Airport::findOrFail($request->input('airport_id'));

            $manage_airport = Bouncer::ability()->firstOrCreate([
                'name' => 'manage-airport-' . $airport->id,
                'title' => 'Manage Airport ' . $airport->name,
            ]);

            $abilities = $user->getAbilities();
            foreach ($abilities as $ability) {
                if (strpos($ability->name, 'manage-airport-') !== false) {
                    Bouncer::disallow($user)->to($ability);
                }
            }
            Bouncer::allow($user)->to($manage_airport);
            return redirect()->route('super.users.index')->with('success', 'Permisos de usuario actualizados');
        }
        return redirect()->route('super.users.index')->with('error', 'Este usuario no tiene rol de administrado de aeropuerto');
    }

    public function removeAirportPermission(User $user)
    {
        $abilities = $user->getAbilities();
        foreach ($abilities as $ability) {
            if (strpos($ability->name, 'manage-airport-') !== false) {
                Bouncer::disallow($user)->to($ability);
            }
        }
        return redirect()->route('super.users.index')->with('success', 'Permisos del usuario para el aeropuerto removidos!');
    }

    public function showGiveAirLinePermission(User $user)
    {
        $user_auth = Auth::user();
        list($sidebar, $header, $footer) = VoyargeHelper::instance()->GetDashboard($user_auth);
        return view('super.users.give_airline', compact('user', 'sidebar', 'header', 'footer'));
    }

    public function giveAirlinePermission(Request $request, User $user)
    {
        $request->validate([
            'airline_id' => 'required|integer|exists:airports,id'
        ]);
        if ($user->can('manage-airline')) {
            $airport = Airline::findOrFail($request->input('airline_id'));

            $manage_airport = Bouncer::ability()->firstOrCreate([
                'name' => 'manage-airline-' . $airport->id,
                'title' => 'Manage Airline ' . $airport->short_name,
            ]);

            $abilities = $user->getAbilities();
            foreach ($abilities as $ability) {
                if (strpos($ability->name, 'manage-airline-') !== false) {
                    Bouncer::disallow($user)->to($ability);
                }
            }
            Bouncer::allow($user)->to($manage_airport);
            return redirect()->route('super.users.index')->with('success', 'Permisos de usuario actualizados');
        }
        return redirect()->route('super.users.index')->with('error', 'Este usuario no tiene rol de administrador de Aerolinea');
    }

    public function removeAirlinePermission(User $user)
    {
        $abilities = $user->getAbilities();
        foreach ($abilities as $ability) {
            if (strpos($ability->name, 'manage-airline-') !== false) {
                Bouncer::disallow($user)->to($ability);
            }
        }
        return redirect()->route('super.users.index')->with('success', 'Permisos del usuario para el Aerolinea removidos!');
    }
}
