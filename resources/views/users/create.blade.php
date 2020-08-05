@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                            <h2>Add New User</h2>
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="name"><strong>Name:</strong></label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" placeholder="Ex. John Doe">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="username"><strong>Username:</strong></label>
                                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', isset($user) ? $user->username : '') }}" placeholder="Ex. jhon.doe">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label for="phone_number"><strong>Phone Number:</strong></label>
                                            <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', isset($user) ? $user->phone_number : '') }}" placeholder="Ex. 72269345">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label for="birthday"><strong>Birthday:</strong></label>
                                            <input id="birthday" data-provide="datepicker" type="text" class="form-control @error('birthday') is-invalid @enderror" name="birthday" value="{{ old('birthday', isset($user) ? $user->birthday : '') }}" required autocomplete="birthday" autofocus>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4">
                                        <div class="form-group">
                                            <label for="email"><strong>Email:</strong></label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', isset($user) ? $user->email : '') }}" placeholder="Ex. jhon.doe@gmail.com" required autocomplete="email">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-3">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                    <label for="password-confirm" class="col-md-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-3">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
