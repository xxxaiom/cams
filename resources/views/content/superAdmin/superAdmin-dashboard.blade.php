@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
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
                                <tr>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('storage/js/superAdmin/dashboard.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
