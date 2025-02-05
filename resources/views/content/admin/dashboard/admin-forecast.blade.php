@extends('layouts/contentNavbarLayout')

@section('title', 'Reports')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="input-group input-group-md mb-3" style="width: 235px;">
                        <select class="form-select" aria-label="Default select example" id="selectType">
                            <option disabled selected>Select type</option>
                            <option value="crime">Crime</option>
                            <option value="accidents">Accidents</option>
                        </select>
                        <button class="btn btn-primary" type="button" id="clickRScript">Forecast</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <div id="ts">
                        <h5>No plot</h5>
                    </div>
                    <div id="tsPlotContainer"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body" style="overflow-x: auto;">
                    <div id="ar">
                        <h5>No plot</h5>
                    </div>
                    <div id="autoArimaPlotContainer"></div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page-script')
    <script src="{{ asset('storage/js/admin/forecast.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
