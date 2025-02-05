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
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReport">
                            Create Report
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>crime location</th>
                                    <th>crime description</th>
                                    <th>created at</th>
                                    <th>status</th>
                                    <th>view</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @foreach ($reports as $report)
                                    <tr class="row-{{ $report->id }}">
                                        <td>{{ $report->id }}</td>
                                        <td>{{ $report->crime_location }}</td>
                                        <td>{{ $report->crime_description }}</td>
                                        <td>{{ \Carbon\Carbon::parse($report->created_at)->format('F d, Y h:i A') }}</td>
                                        <td class="status">
                                            <span
                                                class="badge rounded-pill 
                                                    @if ($report->status === 'responding') bg-info
                                                    @elseif($report->status === 'pending')
                                                      bg-danger
                                                    @elseif($report->status === 'responded')
                                                      bg-success
                                                    @else
                                                      bg-secondary  <!-- Optional: Default class for any other status --> @endif">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button"
                                                class="btn rounded-pill btn-primary"data-bs-toggle="modal"
                                                data-bs-target="#policeLocationModal"
                                                onclick="showPoliceLocation({{ $report->id }})">
                                                <i class='bx bx-map me-1'></i>View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Report -->
    <div class="modal fade" id="createReport" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Create a report</h5>
                    <button type="button" class="btn-close close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <small class="modal-title" id="message"></small>
                <div id="firstPage">
                    <form id="create_reports">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="location" class="form-label">Location of crime</label>
                                        <a href="javascript:void(0)" class="small" onclick="showMap()"><i
                                                class="bx bx-current-location"></i>Show Map</a>
                                    </div>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class='bx bx-current-location'></i></span>
                                        <input type="text" class="form-control" id="crime_location" name="crime_location"
                                            oninput="check('crime_location')" placeholder="Open Map" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="crime" class="form-label">crime</label>
                                    <select class="form-select" aria-label="Default select example" id="crime"
                                        name="crime" oninput="check('crime')">
                                        <option selected disabled>Select a crime</option>
                                        @foreach ($crimes as $crime)
                                            <option value="{{ $crime->name }}">{{ $crime->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Brief description</label>
                                    <input type="text" class="form-control" id="crime_description"
                                        name="crime_description" oninput="check('crime_description')">
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label for="location" class="form-label">Share your location</label>
                                        <small><a href="" id="small"></a></small>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="share_location"
                                            name="share_location" oninput="check('share_location')">
                                        <label class="form-check-label" for="terms-conditions">
                                            I agree to
                                            <a href="javascript:void(0);">privacy policy & terms</a>
                                        </label>
                                    </div>
                                    <input type="hidden" id="lat" name="lat" value="">
                                    <input type="hidden" id="long" name="long" value="">
                                    <input type="hidden" id="accuracy" name="accuracy" value="">
                                    <input type="hidden" id="reportLat" name="reportLat" value="">
                                    <input type="hidden" id="reportLong" name="reportLong" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary close-btn"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

                <!-- Map -->
                <div id="secondPage" hidden>
                    <div class="modal-body">
                        <div id="userMap" style="height: 500px;"></div>
                    </div>
                </div>

            </div>



        </div>
    </div>

    <!-- Modal Confirm Map Location -->
    <div class="modal fade" id="reportMapLocation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="title-modal">Confirm Location?</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmBrgy()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Police Location -->
    <div class="modal fade" id="policeLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Police Real Time Location</h5>
                    <button type="button" class="btn-close close-modal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="policeMapLocation" style="height: 750px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('storage/js/user/submit-report.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
