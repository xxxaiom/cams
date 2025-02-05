@extends('layouts/contentNavbarLayout')

@section('title', 'Admin Information')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">Account Details</h5>
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block rounded"
                            height="100" width="100" id="uploadedAvatar" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" class="account-file-input" hidden
                                    accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                <div class="card-body">
                    <form id="admin_details">
                        <div id="message"></div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input class="form-control" type="text" id="firstName" name="firstName"
                                value="{{ $details->first_name ?? '' }}" autofocus disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input class="form-control" type="text" name="middleName" id="middleName"
                                value="{{ $details->middle_name ?? '' }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input class="form-control" type="text" name="lastName" id="lastName"
                                value="{{ $details->last_name ?? '' }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="suffix" class="form-label">Suffix</label>
                                <input class="form-control" type="text" name="suffix" id="suffix" 
                                value="{{ $details->suffix ?? '' }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender"
                                    aria-label="Default select example" disabled>
                                    <option disabled selected>Select gender</option>
                                    <option value="Male" {{ ($details->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ ($details->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="birthDate" class="form-label">Birth Date</label>
                                <input class="form-control" type="text" name="birthDate" id="birthDate"
                                value="{{ $details->birthdate ?? '' }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                <input class="form-control" type="text" name="phoneNumber" id="phoneNumber"
                                    maxlength="11" pattern="^\d{11}$" value="{{ $details->number ?? '' }}" disabled/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="civil_status" class="form-label">Civil Status</label>
                                <select class="form-select" id="civil_status" name="civil_status"
                                    aria-label="Default select example" disabled>
                                    <option disabled selected>Select civil status</option>
                                    <option value="Single" {{ ($details->civil_status ?? '') === 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ ($details->civil_status ?? '') === 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Divorced" {{ ($details->civil_status ?? '') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Widowed" {{ ($details->civil_status ?? '') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input class="form-control" type="text" name="address" id="address" value="{{ $details->address ?? '' }}" disabled/>
                            </div>
                        </div>
                        <div class="mt-2" id="hideEdit">
                            <button type="button" id="edit" class="btn btn-primary me-2">Edit details?</button>
                        </div>
                        <div class="mt-2" id="showSubmit" style="display: none;">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="button" id="cancelEdit" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>

                <hr class="my-0">

                <h5 class="card-header">Account Credentials</h5>
                <div class="card-body">
                    <div id="messageError"></div>
                    <form id="changePassword">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input class="form-control" type="text" name="username" id="username"
                                    placeholder="Enter username" disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="d-flex justify-content-start gap-2">
                                    <label for="old_password" class="form-label">Password</label>
                                    <div class="small" id="errorMessage" style="color: red"></div>
                                </div>
                                <input class="form-control" type="text" name="old_password" id="old_password"
                                    placeholder="Enter current password" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="d-flex justify-content-start gap-2">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="small" id="checkOldPass" style="color: red"></div>
                                </div>
                                <input class="form-control" type="text" name="password" id="password" disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="d-flex justify-content-start gap-2">
                                    <label for="password_confirmation" class="form-label">Retype New Password</label>
                                    <div class="small" id="checkSamePass" style="color: red"></div>
                                </div>
                                <input class="form-control" type="text" name="password_confirmation"
                                    id="password_confirmation" disabled />
                            </div>
                        </div>
                        <div class=" mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script src="{{ asset('storage/js/admin/account_details.js?id=' . Illuminate\Support\Carbon::now() . '') }}"></script>
@endsection
