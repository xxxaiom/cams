@extends('layouts/contentNavbarLayout')

@section('title', 'Admin Accounts')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="">Admin Accounts</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAdmin">
                        New Admin
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>username</th>
                                    <th>password</th>
                                    <th>role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->password }}</td>
                                        <td>{{ $user->role }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newAdmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Login Credentials</h5>
                    <button type="button" class="btn-close close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="newAdminCred">
                    <div class="modal-body">
                        <div id="message"></div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Username:</span>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="username">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Password:</span>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Confirm Password:</span>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('storage/js/superAdmin/accounts-admin.js?id=' . Illuminate\Support\Carbon::now() . '') }}">
    </script>
@endsection
