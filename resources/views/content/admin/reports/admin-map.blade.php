@extends('layouts/contentNavbarLayout')

@section('title', 'Map')

@section('content')

    <style>
        .popup-content {
            padding: 10px;
            font-family: Arial, sans-serif;
        }

        .popup-content h5 {
            margin-bottom: 10px;
        }

        .popup-content span {
            display: inline-block;
            margin-bottom: 5px;
        }

        .custom-label {
            background: none !important;
            border: none !important;
            color: whitesmoke;
            font-size: 13px;
            transform: scale(1);
            transform-origin: top left;
            white-space: nowrap;
        }
    </style>



    <div id="alertMessage">
        <div class="alert alert-info d-flex align-items-center gap-2" role="alert"
            style="width: max-content; padding: 10px;">
            <i class='bx bx-info-circle'></i>
            <div>
                Receiving real-time reports...
            </div>
        </div>
    </div>

    <div class="row" id="row1">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    @if (Session::get('user_id') === auth()->user()->id)
                        <div id="map" style="height: 750px"></div>
                        <audio id="notificationSound" src="assets/audio/emergency-sound.mp3" preload="auto" loop></audio>
                    @else
                        <div>You are not authorized</div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="row" id="row2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    @if (Session::get('user_id') === auth()->user()->id)
                        <div id="policeMapLocation" style="height: 750px; width: 100%;"></div>
                    @else
                        <div>You are not authorized</div>
                    @endif

                </div>
            </div>
        </div>
    </div>


@endsection

@section('page-script')

    <script>
        const isAuthenticated = @json(Auth::check());
    </script>
    <script src="{{ asset('storage/js/admin/map.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
