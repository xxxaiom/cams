@extends('layouts/contentNavbarLayout')

@section('title', 'Reports')

@section('content')

    <style>
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

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex justify-content-start gap-4">
                            <img src="{{ asset('assets/img/illustrations/investigation.png') }}" alt=""
                                style="width: 60px;">
                            <div class="d-flex flex-column">
                                <h5>Reports</h5>
                                <span>{{ $reports->count() }}</span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                <a class="dropdown-item" href="javascript:void(0);">Last Week</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">All Time</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex justify-content-start gap-4">
                            <img src="{{ asset('assets/img/illustrations/handcuff.png') }}" alt=""
                                style="width: 60px;">
                            <div class="d-flex flex-column">
                                <h5>Crime</h5>
                                <span>{{ $incidents->count() }}</span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex justify-content-start gap-4">
                            <img src="{{ asset('assets/img/illustrations/accident.png') }}" alt=""
                                style="width: 60px;">
                            <div class="d-flex flex-column">
                                <h5>Accident</h5>
                                <span>{{ $accidents->count() }}</sp4an>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="d-flex justify-content-between card-header">
                    <h5>Heat Map Reports for each Barangay</h5>
                    <select class="form-select" name="selectHeatMap" id="selectHeatMap" style="width: 120px;">
                        <option value="">All years</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                <div class="card-body">
                    <div id="map" style="height: 600px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="d-flex justify-content-between card-header">
                    <h5></h5>
                    <select class="form-select" name="selectColorBarangay" id="selectColorBarangay" style="width: 120px;">
                        <option value="">All Years</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <div class="card-body">

                    <div id="mapColor" style=" height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Incident Reports from 2016 - 2024</h5>
                        <select class="form-select" id="reportSelect" aria-label="Default select example"
                            style="width: 200px;">
                            <option value="0">Reports per day</option>
                            <option value="1">Reports per month</option>
                            <option value="2">Reports per year</option>
                        </select>
                    </div>
                    <div style="overflow-x: auto;">
                        <div id="first_chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Number of reports for every offense [2016-2024]</h5>
                    <div>
                        <div id="second_chart" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="d-flex justify-content-between card-header">
                    <h5>Distribution of Reported Incident each year</h5>
                    <select class="form-select" name="selectColorBarangay" id="selectColorBarangay"
                        style="width: 120px;">
                        <option value="">All Years</option>
                        <option value="2016">2016</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <div class="card-body">
                    wew
                </div>
            </div>
        </div>
    </div>




@endsection

@section('page-script')
    <script src="{{ asset('storage/js/admin/dashboard.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
    <script src="{{ asset('storage/js/admin/leaflet-heat.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
