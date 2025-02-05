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

        .myInput {
            border: none;
            /* Removes the border */
            background: transparent;
            /* Makes the background transparent */
            outline: none;
            /* Removes the focus outline */
            padding: 10px;
            /* Adds some padding for spacing */
            font-size: 16px;
            /* Customize the font size */
        }
    </style>

    <!-- Modal -->
    <div class="modal fade" id="largeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="table-responsive" style="width: 100%;">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center" style="width: 150px;">
                                        <small>FOR POLICE BLOTTER ENCODER USE ONLY</small>
                                    </td>
                                    <td colspan="6" rowspan="5" class="text-center">
                                        <div>
                                            <h5 class="fw-bold">PHILIPPINE NATIONAL POLICE</h5>
                                            <h1 class="fw-bolder">INCIDENT RECORD FORM</h1>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" rowspan="2" style="width: 150px;">
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex justify-content-start">
                                                <small>BLOTTER ENTRY NUMBER</small>
                                            </div>
                                            <input class="myInput" type="text" id="myFirstInput"
                                                placeholder="Type something...">

                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                    <td colspan="2" rowspan="2" style="width: 150px;">
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex justify-content-start">
                                                <small>TYPE OF INCIDENT</small>
                                            </div>
                                            <input class="myInput" type="text" id="mySecondInput"
                                                placeholder="Type something...">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <p><span class="fw-bold">INSTRUCTIONS: </span> Refer to PNP SOP on
                                            'Recording of
                                            Incidents in the Police Blotter' in filling up this form. This Incident Record
                                            Form (IRF) may be reproduced, photocopied, and/or downloaded from the DIDM
                                            website, www.didm.pnp.gov.ph
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1.5" rowspan="2" style="width: 100px;">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-start">
                                                <small>DATE AND TIME REPORTED:</small>
                                            </div>
                                            <input class="myInput" type="text" id="myThirdInput"
                                                placeholder="Type something...">
                                        </div>
                                    </td>
                                    <td colspan="1.5" rowspan="2" style="width: 100px;">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-start">
                                                <small>DATE AND TIME OF INCIDENT:</small>
                                            </div>
                                            <input class="myInput" type="text" id="myThFourthInput"
                                                placeholder="Type something...">
                                        </div>
                                    </td>
                                    <td class="text-center" colspan="5" rowspan="2">
                                        <h5>ITEM "A" - REPORTING PERSON</h5>
                                    </td>
                                </tr>
                                <tr>
                                </tr>
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
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary" id="openLargeModal">wew</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New Reports</h5>
                    <div class="table-responsive">
                        <table class="table" id="newReportTable">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Type of Report</th>
                                    <th>Description</th>
                                    <th>Reported By</th>
                                    <th>Status</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @if ($newReports->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center">No new reports</td>
                                    </tr>
                                @else
                                    @foreach ($newReports as $report)
                                        <tr class="text nowrap">
                                            <td>{{ $report->crime_location }}</td>
                                            <td>{{ $report->crime_description }}</td>
                                            <td>{{ $report->crime_name }}</td>
                                            <td>{{ $report->reportedBy }}</td>
                                            <td>{{ $report->status }}</td>
                                            <td>
                                                <button type="button" class="btn btn-icon btn-primary"
                                                    onclick="viewInModal('{{ $report->reports_id }}')">
                                                    <span class="tf-icons bx bx-show-alt"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="m-0 mb-5">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <h5 class="card-title">Reports Table</h5>
                    <div class="d-flex justify-content-between mb-2 gap-2">
                        <div class="input-group input-group-merge" style="width: 300px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input id="search" type="text" class="form-control" placeholder="Search..." />
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span>Time Frame: </span>
                            <select name="" id="selectTimeFrame" class="form-select" style="width: 150px;">
                                <option value="0">Today</option>
                                <option value="1">Last Week</option>
                                <option value="2">Last Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="reportsTable">
                            <thead>
                                <tr>
                                    <th>Incident Name</th>
                                    <th>Location</th>
                                    <th>Reported on</th>
                                    <th>Reported by</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                @if ($viewFullReports->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">There are no data</td>
                                    </tr>
                                @else
                                    @foreach ($viewFullReports as $fullreport)
                                        <tr>
                                            <td>{{ $fullreport->incident_name }}</td>
                                            <td>{{ $fullreport->location_description }}</td>
                                            <td>{{ $fullreport->reportedOn }}
                                            </td>
                                            <td>{{ $fullreport->reportedBy }}</td>
                                            <td>
                                                <button type="button" class="btn btn-icon btn-primary"
                                                    onclick="viewFullReports({{ $fullreport->incident_id }})">
                                                    <span class="tf-icons bx bx-show-alt"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Incident Modal -->
    <div class="modal fade" id="reportsDetails" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Details of Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="firstPage">
                    <div class="modal-body">
                        <div id="errorMessage"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label">Crime Location</label>
                                    <a href="javascript:void(0)" class="small show-report-location"><i
                                            class="bx bx-current-location"></i>Show Map</a>
                                </div>
                                <input type="text" class="form-control" id="crime_location" name="crime_location"
                                    disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Crime Description</label>
                                <input type="text" class="form-control" id="crime_description"
                                    name="crime_description" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reported By</label>
                                <input type="text" class="form-control" id="reported_by" name="reported_by" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date and Time Reported</label>
                                <input type="text" class="form-control" id="date_reported" name="date_reported"
                                    disabled>
                            </div>
                        </div>

                        <hr class="m-0 mb-3 mt-3">
                        <div class="mb-3">
                            <h5 class="modal-title" id="staticBackdropLabel">Incident Report</h5>
                        </div>
                        <form id="incident_report">
                            <div class="row">
                                <input type="hidden" id="report_id" name="report_id">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Incident Name</label>
                                    <input type="text" class="form-control" id="incident_name" name="incident_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Incident Description</label>
                                    <input type="text" class="form-control" id="incident_description"
                                        name="incident_description">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Incident Location</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="location_description"
                                            name="location_description">
                                        <button class="btn btn-outline-secondary" type="button" id="map-show"><i
                                                class="bx bx-current-location"></i>Map</button>
                                    </div>
                                </div>
                                <input type="hidden" id="lat" name="lat">
                                <input type="hidden" id="long" name="long">
                                <input type="hidden" id="location" name="location">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date and Time Committed</label>
                                    <input type="text" class="form-control" id="dateTime_committed"
                                        name="dateTime_committed">
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>


                <div id="secondPage" hidden>
                    <div class="modal-body">
                        <div id="addLocation" style="height: 500px;"></div>
                    </div>
                </div>

                <div id="thirdPage" hidden>
                    <div class="modal-body">
                        <div id="viewReportLocation" style="height: 500px;"></div>
                        <input type="hidden" id="reportLatitude" value="">
                        <input type="hidden" id="reportLongitude" value="">
                        <input type="hidden" id="reportAccuracy" value="">
                        <input type="hidden" id="reportBarangay" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="incidentReportDetails" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Incident Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick=""></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="">Close</button>
                    <button type="button" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmLocation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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


    <div class="modal fade" id="praktisMapModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="title-modal">Map</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="practiceMap" style="height: 500px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('page-script')
    <script src="{{ asset('storage/js/admin/report.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
