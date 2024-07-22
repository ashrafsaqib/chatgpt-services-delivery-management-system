@extends('site.layout.app')
<style>
    .error-alert {
    width: 100%;
    padding: 9px 57px;
    font-size: 80%;
    color: #dc3545;
    }
    .success-alert {
    width: 100%;
    padding: 9px 57px;
    font-size: 80%;
    color: #199700;
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
                
                @if(Session::has('error'))
                    <span class="alert alert-danger" role="alert">
                        <strong>{{ Session::get('error') }}</strong>
                    </span>
                @endif
                @if(Session::has('success'))
                <span class="alert alert-success" role="alert">
                        <strong>{{ Session::get('success') }}</strong>
                    </span>
                @endif
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.post-login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                
                                <a class="btn btn-link" href="{{ route('customer.registration') }}">
                                    Register
                                </a><br>
                                <a class="btn btn-link" href="{{ route('customer.registration') }}?type=Affiliate">
                                    Register as Affiliate
                                </a><br>
                                <a class="btn btn-link" href="{{ route('customer.registration') }}?type=Freelancer">
                                    Register as Freelancer
                                </a><br>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
