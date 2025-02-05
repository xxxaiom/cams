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
        <div class="col-lg-2 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="fw-bold">Reports</h4>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="changeValue" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" style="min-width: auto;">
                            <a class="dropdown-item cursor-pointer" id="today">Today</a>
                            <a class="dropdown-item cursor-pointer" id="month">Last Month</a>
                            <a class="dropdown-item cursor-pointer" id="all">All Time</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4 pb-3 pt-3">
                        <h1 id="countValue" style="margin: 0;">{{ $count }}</h1>
                        <p id="sortDate" style="margin: 0;">today</p>
                    </div>
                    <div class="mb-4">
                        <small>Crime</small>
                        <div class="progress">
                            <div class="progress-bar crime-bar" role="progressbar" style="width: 25%" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">25%</div>
                        </div>
                    </div>
                    <div>
                        <small>Accident</small>
                        <div class="progress">
                            <div class="progress-bar accident-bar" role="progressbar" style="width: 75%" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100">75%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center card-header pb-0">
                    <h5><span id="chartTitle">All </span> Reports for each year</h5>
                    <div class="pt-0">
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" style="min-width: auto;">
                                <a class="dropdown-item cursor-pointer" id="All">All Reports</a>
                                <a class="dropdown-item cursor-pointer" id="Crime">Crimes</a>
                                <a class="dropdown-item cursor-pointer" id="Accident">Accidents</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <div id="chart" style="height: 265px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center card-header pb-0">
                    <h5><span id="secondChartTitle">Reports </span>Distribution across the day</h5>
                    <div class="pt-0">
                        <select class="form-select" name="selectDonutData" id="selectDonutData" style="width: 110px;">
                            <option value="donutAll">All Time</option>
                            <option value="donutCrime">Crime</option>
                            <option value="donutAccident">Accident</option>
                        </select>
                    </div>

                </div>
                <div class="card-body pb-0">
                    <div id="secondChart" style="height: 260px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center card-header">
                    <h5>Heat Map</h5>
                    <select class="form-select" id="heatMap" style="width: 110px;">
                        <option value="">All Time</option>
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
                    <div id="map" style="height: 495px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="d-flex justify-content-between card-header">
                    <h5>Barangay with most reports</h5>
                    <select class="form-select" id="barangayReports" style="width: 110px;">
                        <option value="">All Time</option>
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
                    <div class="table-responsive">
                        <table class="table" id="brgyTable">
                            <thead>
                                <tr>
                                    <th>no</th>
                                    <th>barangay</th>
                                    <th>crime</th>
                                    <th>accident</th>
                                    <th>total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allData as $data)
                                    <tr class="text-nowrap">
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $loop->iteration }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->barangay_name }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->crimeReports }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->accidentReports }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->totalReports }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="d-flex justify-content-between card-header pb-0">
                    <h5>Reports per barangay</h5>
                    <select class="form-select" id="selectBar" style="width: 130px;">
                        <option value="allReports">All Reports</option>
                        <option value="crime">Crime</option>
                        <option value="accident">Accident</option>
                        <option value="fullList" id="fullList">Full List</option>
                    </select>
                </div>
                <div class="card-body pb-0">
                    <div id="bar" style="height: 365px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="fullListModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="staticBackdropLabel">Full List</h5>
                        <select class="form-select" id="barangayReports" style="width: 110px;">
                            <option value="">All Time</option>
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

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>no</th>
                                    <th>barangay</th>
                                    <th>crime</th>
                                    <th>accident</th>
                                    <th>total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allData as $data)
                                    <tr class="text-nowrap">
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $loop->iteration }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->barangay_name }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->crimeReports }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->accidentReports }}</td>
                                        <td style="color: {{ $loop->iteration <= 3 ? 'red' : '' }}">
                                            {{ $data->totalReports }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    wew
                </div>
            </div>
        </div>
    </div>



@endsection

@section('page-script')
    <script src="{{ asset('storage/js/admin/maps.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('storage/js/admin/leaflet-heat.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
