<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
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
        return view('users.create');
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
            'username' => ['required', 'string', 'max:191', 'unique:users'],
            'email' => ['required', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number'=>['required', 'numeric'],
            'birthday'=>['required', 'date_format:d/m/Y'],
        ]);

        $user = new User([
            'password' => Hash::make($request->input('password')),
            'username' => $request->input('username'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number'=>$request->input('phone_number'),
            'birthday'=>Carbon::createFromFormat('d/m/Y', $request->input('data')['attributes']['birthday']),
        ]);

        $user ->save();

        return redirect()->route('users.index')->with('success','User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Application|Factory|View|void
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Application|Factory|View|void
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse|Response|void
     */
    public function update(Request $request, User $user)
    {

        $rules = [
            'name' => ['required', 'string', 'max:191'],
            'username' => ['required', 'string', 'max:191', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone_number'=>['required', 'numeric'],
            'birthday'=>['required', 'date_format:d/m/Y'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->route('users.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->input('password'))
            $password = Hash::make($request->input('password'));
        else
            $password = $user->getAuthPassword();

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = $password;
        $user->birthday = Carbon::createFromFormat('d/m/Y', $request->input('data')['attributes']['birthday']);
        $user->phone_number = $request->input('phone_number');

        $user->save();

        return redirect()->route('users.index')->with('success','User created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse|Response|void
     * @throws Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success','User Deleted successfully.');
    }

    /**
     * Delete all selected User at once.
     *
     * @return Response|void
     */
    public function mass()
    {
        User::whereIn('id', request('ids'))->delete();
        return response()->noContent();
    }
}
