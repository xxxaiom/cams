@extends('layouts/blankLayout')

@section('title', 'Admin Login')

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body" style="padding-bottom: 0;">
                        <div class="app-brand justify-content-center">
                            <a class="app-brand-link gap-2">
                                <img src="{{ asset('assets/img/illustrations/pnp.png') }}" alt="Brand Logo"
                                    class="app-brand-logo" style="width: 30px; height: auto;">
                                <span class="app-brand-text text-body fw-bold" style="font-size: 1rem;">Sogod Police
                                    Management System</span>
                            </a>
                        </div>
                        <div class="p-4 pt-0">
                            <div id="message"></div>
                            <form id="login">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter your username" autofocus>
                                </div>
                                <div class="mb-3 form-password-toggle">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label" for="password">Password</label>
                                        <a href="{{ url('auth/forgot-password-basic') }}">
                                            <small>Forgot Password?</small>
                                        </a>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-me">
                                        <label class="form-check-label" for="remember-me">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('page-script')
<script>
    const userRole = '{{ Session::get('user_role') }}';
</script>

    <script src="{{ asset('storage/js/login/admin-login.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
