<?php

use App\Http\Controllers\superAdmin\AdminAccountsController;
use App\Http\Controllers\superAdmin\UserAccountsController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminDashboardForecast;
use App\Http\Controllers\admin\AdminInfoController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminMapController;
use App\Http\Controllers\admin\AdminMapGraphsController;
use App\Http\Controllers\admin\AdminReportsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\login\LoginController;
use App\Http\Controllers\superAdmin\SuperAdminDashbaord;
use App\Http\Controllers\tables\Basic as TablesBasic;
use App\Http\Controllers\user\UserDashboardController;
use App\Http\Controllers\user\UserSubmitReport;
use App\Http\Middleware\SuperAdmin;

Route::post('/register_user', [LoginController::class, 'registerUser'])->name('register_user');

// For System Login
Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');

    Route::post('/user_login', [LoginController::class, 'userLogin'])->name('user_login');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

// For Admin Login
Route::get('/admin-login-view', [AdminLoginController::class, 'index'])->name('admin-login');
Route::post('/admin-login', [AdminLoginController::class, 'adminLogin'])->name('admin_login');

// SuperAdmin 
Route::middleware(['auth', 'share', 'superAdmin'])->group(function () {
    // Dashboard
    Route::get('/superAdmin-dashboard', [SuperAdminDashbaord::class, 'index'])->name('superAdmin-dashboard');
    // Admin Accounts
    Route::get('/accounts-admin', [AdminAccountsController::class, 'index'])->name('accounts-admin');
    Route::post('/new-admin', [AdminAccountsController::class, 'newAdmin'])->name('new-admin');
    // User Accounts
    Route::get('/accounts-user', [UserAccountsController::class, 'index'])->name('accounts-user');
});


// Admin
Route::middleware(['auth', 'share', 'admin'])->group(function () {
    // For Admin Dashboard - Graphs
    Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('dashboard-graphs');
    Route::get('/fetch-data', [AdminDashboardController::class, 'fetchData'])->name('fetch-data');
    Route::get('/fetch-map-boundaries', [AdminDashboardController::class, 'fetchMapBoundaries'])->name('fetch-map-boundaries');
    Route::get('/fetch-heat-map', [AdminDashboardController::class, 'fetchHeatMap'])->name('fetch-heat-map');
    Route::get('/add-color', [AdminDashboardController::class, 'addColor'])->name('add-color');
    Route::get('/runRScript', [AdminDashboardController::class, 'runRScript'])->name('run-r-script');


    // For Admin Dashboard - Map
    Route::get('/admin-maps-graphs', [AdminMapGraphsController::class, 'index'])->name('dashboard-maps');
    Route::get('/changeReportValue', [AdminMapGraphsController::class, 'changeReportValue'])->name('changeReportValue');
    Route::get('/getAllData', [AdminMapGraphsController::class, 'getAllData'])->name('getAllData');
    Route::get('/getDonutData', [AdminMapGraphsController::class, 'getDonutData'])->name('getDonutData');
    Route::get('/fetch-map-boundaries', [AdminMapGraphsController::class, 'fetchMapBoundaries'])->name('fetchMapBoundaries');
    Route::get('/fetch-heat-map', [AdminMapGraphsController::class, 'fetchHeatMap'])->name('fetch-heat-map');
    Route::get('/barData', [AdminMapGraphsController::class, 'getBarData'])->name('get-bar-data');
    Route::get('/fetch-most-reports', [AdminMapGraphsController::class, 'fetchMostReports'])->name('fetch-most-reports');

    // For Admin Dashboard - Forecast
    Route::get('/admin-forecast', [AdminDashboardForecast::class, 'index'])->name('dashboard-forecast');
    Route::get('/runRScript', [AdminDashboardForecast::class, 'runRScript'])->name('run-r-script');

    // For Admin Reports
    Route::get('/admin-reports', [AdminReportsController::class, 'index'])->name('reports-admin-reports');
    Route::get('/fetch-report-data', [AdminReportsController::class, 'fetchTimeFrame'])->name('fetch-data-reports');
    Route::post('/search-report', [AdminReportsController::class, 'searchReports'])->name('search-reports');
    Route::get('/fetch-modal-data', [AdminReportsController::class, 'fetchModalData'])->name('fetch-modal-data');
    Route::post('/createIncidentReport', [AdminReportsController::class, 'incidentReport'])->name('create-incident-report');
    Route::get('/fetch-new-reports', [AdminReportsController::class, 'fetchNewReports'])->name('fetch-new-reports');
    Route::get('/fetch-reports-data', [AdminReportsController::class, 'fetchFullReports'])->name('fetch-reports-data');
    Route::get('/fetch-map-boundaries', [AdminReportsController::class, 'fetchMapBoundaries'])->name('fetch-map-boundaries');
    Route::get('/get-brgy-names-admin', [AdminReportsController::class, 'getBarangay'])->name('get-brgy-names');

    Route::get('/get-brgy-names', [AdminReportsController::class, 'getBarangay'])->name('get-brgy-name');
    // For Admin Map
    Route::get('/admin-map', [AdminMapController::class, 'index'])->name('reports-admin-live-map');
    Route::get('/get_latest_reports', [AdminMapController::class, 'newReports'])->name('newReports');
    Route::post('/report_received', [AdminMapController::class, 'reportReceived'])->name('reportReceived');
    Route::get('/fetch-boundaries', [AdminMapController::class, 'fetchBoundaries'])->name('fetch-boundaries');
    Route::get('/checkExistingReport', [AdminMapController::class, 'checkExistingReport'])->name('check-existing-report');

    // For Admin Info
    Route::get('/admin-info', [AdminInfoController::class, 'index'])->name('info-admin');
    Route::post('/update-admin-details', [AdminInfoController::class, 'updateAdminDetails'])->name('update-admin-details');
    Route::get('/getOldPassword', [AdminInfoController::class, 'getOldPassword'])->name('get-old-password');
    Route::get('/confirmNewPassword', [AdminInfoController::class, 'confirmNewPassword'])->name('confirm-new-password');
    Route::post('/changeAdminPassword', [AdminInfoController::class, 'changeAdminPassword'])->name('change-admin-password');
});



// User
Route::middleware(['auth', 'share', 'user'])->group(function () {
    // For User Dashboard
    Route::get('/user-dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');
    Route::get('/fetch-brgy-boundaries', [UserDashboardController::class, 'fetchBoundaries'])->name('fetch-brgy-boundaries');
    Route::get('/getPoliceLocation', [AdminMapController::class, 'getLocation'])->name('get-police-location');

    // For User Report
    Route::get('/user-submit-report', [UserSubmitReport::class, 'index'])->name('user-submit-report');
    Route::post('/submit-report', [UserSubmitReport::class, 'submitReport'])->name('submit-report');
    Route::get('/get-latest-status', [UserSubmitReport::class, 'getlatestReport'])->name('get-latest-status');
    Route::get('/fetch-user-map-boundaries', [UserSubmitReport::class, 'fetchBoundaries'])->name('fetch-user-map-boundaries');
    Route::get('/get-brgy-names-user', [UserSubmitReport::class, 'getBarangay'])->name('get-brgy-names');
});



// Main Page Route
Route::get('/', function () {
    return redirect()->route('login'); // Redirect to the login route
});

// layout
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name('pages-account-settings-account');
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name('pages-account-settings-notifications');
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name('pages-account-settings-connections');
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name('pages-misc-under-maintenance');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

// icons
Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');
