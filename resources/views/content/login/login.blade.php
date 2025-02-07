@extends('layouts/blankLayout')

@section('title', 'Login Page')

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
                            <div id="user_message"></div>
                            <form id="user_login">
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
                                        <span class="input-group-text cursor-pointer" onclick="showPassword()" id="showpass"><i class="bx bx-hide"></i></span>
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

                            <p class="text-center">
                                <span>Don't have an account?</span>
                                <a href="#"data-bs-toggle="modal" data-bs-target="#register_user">
                                    <span>Create an account</span>
                                </a>
                            </p>
                        </div>



                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- /User Create Modal -->

    <form id="registerCitizenInfo" method="post">
        @csrf
        <div class="modal fade" id="register_user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Register User</h5>
                        <button type="button" class="btn-close close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <small class="modal-title" id="register_message" style="width: 50%"></small>

                    <form id="register_form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">firstname</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                            placeholder="John Paul">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">middlename</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" class="form-control" id="middlename" name="middlename"
                                            placeholder="Rodulf">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">lastname</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" class="form-control" id="lastname" name="lastname"
                                            placeholder="Alboracin">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">gender</label>
                                    <select class="form-select" aria-label="Default select example" id="gender"
                                        name="gender">
                                        <option selected>Select a gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Secret">Secret</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">birthdate</label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate"
                                        placeholder="Enter your username">
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">civil status</label>
                                    <select class="form-select" aria-label="Default select example" id="civil_status"
                                        name="civil_status">
                                        <option selected>Select a civil status</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>

                            </div>
                            
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="email" class="form-label">contact number</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                        <input type="tel" class="form-control" id="number" name="number"
                                            placeholder="09123456789">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-8">
                                    <label for="email" class="form-label">email</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-message-square"></i></span>
                                        <input type="tel" class="form-control" id="email" name="email"
                                            placeholder="johnpaul@gmail.com">
                                    </div>
                                </div>

                            </div>

                            <hr class="mb-4" />

                            <div class="row">

                                <div class="mb-3 col-md-4">
                                    <label for="prov" class="form-label">province</label>
                                    <select class="form-select" aria-label="Default select example" id="prov"
                                        name="prov">
                                        <option selected>Select a province</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="municity" class="form-label">municipality</label>
                                    <select class="form-select" aria-label="Default select example" id="municity"
                                        name="municity">
                                        <option selected>Select a municipality</option>
                                    </select>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label for="bgy" class="form-label">baranggay</label>
                                    <select class="form-select" aria-label="Default select example" id="bgy"
                                        name="bgy">
                                        <option selected>Select a baranggay</option>
                                    </select>
                                </div>
                            </div>

                            <hr class="mb-4" />

                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label for="username" class="form-label">username</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bxs-user-account'></i></span>
                                        <input type="text" class="form-control" id="uname" name="uname"
                                            placeholder="Enter a username">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="password" class="form-label">password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="pword" class="form-control" name="pword"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer" onclick="showPassword()" id="showpass1"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label for="pword_confirmation" class="form-label">Confirm Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="pword_confirmation" class="form-control"
                                            name="pword_confirmation"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            aria-describedby="password" />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="loginuser" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('page-script')
    <script>
        const userRole = '{{ Session::get('user_role') }}';
    </script>
    <script src="{{ asset('storage/js/login/provmunbgy.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
    <script src="{{ asset('storage/js/login/login.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
