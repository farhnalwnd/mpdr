<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Master\CategoryController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\LevelController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SectionController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Mpdr\MpdrController;
use App\Http\Controllers\Mpdr\MpdrApprovalController;
use App\Http\Controllers\Mpdr\MpdrInitiatorController;
use App\Http\Controllers\PreMpdr\PreMpdrController;
use App\Http\Controllers\PreMpdr\PreMpdrApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Rs\RequistionSlipController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Master\LogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/create-symlink', function () {
    $target = storage_path('app/public'); // path ke folder storage/app/public
    $link = public_path('storage'); // path ke folder public/storage

    // Cek apakah symlink sudah ada
    if (!File::exists($link)) {
        // Buat symlink
        symlink($target, $link);
        return 'Symlink has been created.';
    }

    return 'Symlink already exists.';
});


// Pre-MPDR
Route::prefix('prempdr')->group(function () {
    Route::get('/mail-approve', [PreMpdrApprovalController::class, 'approveNotReview'])->name('prempdr.approveNotReview');
    Route::get('/mail-approve-with-review', [PreMpdrApprovalController::class, 'approveWithReview'])->name('prempdr.approveWithReview');
    Route::get('/mail-not-approve', [PreMpdrApprovalController::class, 'notApprove'])->name('prempdr.notApprove');
    Route::POST('/mail-comment', [PreMpdrApprovalController::class, 'mailComment'])->name('prempdr.comment');
    Route::get('/print-{no_reg}', [PreMpdrController::class, 'print'])->name('prempdr.print');
    Route::get('/printdata', [PreMpdrController::class, 'getPrintData'])->name('prempdr.print.data');
});

// MPDR
Route::prefix('mpdr')->group(function () {
    Route::get('/mail-approve', [MpdrApprovalController::class, 'approveNotReview'])->name('mpdr.approveNotReview');
    Route::get('/mail-approve-with-review', [MpdrApprovalController::class, 'approveWithReview'])->name('mpdr.approveWithReview');
    Route::get('/mail-not-approve', [MpdrApprovalController::class, 'notApprove'])->name('mpdr.notApprove');
    Route::POST('/mail-comment', [MpdrApprovalController::class, 'mailComment'])->name('mpdr.comment');
    Route::get('/print-{no_reg}', [MpdrController::class, 'print'])->name('mpdr.print');
    Route::get('/printdata', [MpdrController::class, 'getPrintData'])->name('mpdr.print.data');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard/getPreMpdrTotal', [Dashboard::class, 'getPreMpdrTotal'])->name('dashboard.prempdr.total');
    Route::get('/dashboard/getMpdrTotal', [Dashboard::class, 'getMpdrTotal'])->name('dashboard.mpdr.total');
    Route::get('/dashboard/getPreMpdrPending', [Dashboard::class, 'getPreMpdrPending'])->name('dashboard.prempdr.pending');
    Route::get('/dashboard/getMpdrPending', [Dashboard::class, 'getMpdrPending'])->name('dashboard.mpdr.pending');
    Route::get('/dashboard/getDashboardLogs', [Dashboard::class, 'getDashboardLogs'])->name('dashboard.logs');
    Route::get('/dashboard/getPreMpdrYear', [Dashboard::class, 'getPreMpdrYear'])->name('dashboard.prempdr.year');
    Route::get('/dashboard/getMpdrYear', [Dashboard::class, 'getMpdrYear'])->name('dashboard.mpdr.year');
    Route::get('/dashboard/getPreMpdrChart', [Dashboard::class, 'getPreMpdrChart'])->name('dashboard.prempdr.chart');
    Route::get('/dashboard/getMpdrChart', [Dashboard::class, 'getMpdrChart'])->name('dashboard.mpdr.chart');



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.updates');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('levels', LevelController::class);
    Route::resource('sections', SectionController::class);

    Route::post('/notifications/mark-as-read', [PermissionController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/markAllAsRead', [PermissionController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/count', function () {
        return response()->json(['count' => auth()->user()->unreadNotifications->count()]);
    })->name('notifications.count');
    Route::delete('/notifications/clear', [PermissionController::class, 'clearAll'])->name('notifications.clear');

    Route::get('/getLogsData', [LogController::class, 'getLogsData'])->name('logs.data');


    // =============================================================PRE MPDR=================================================================
    Route::prefix('prempdr')->group(function () {
        Route::get('/', [PreMpdrController::class, 'index'])->name('prempdr.index');
        Route::get('/create', [PreMpdrController::class, 'create'])->name('prempdr.create');
        Route::get('/edit/{id}', [PreMpdrController::class, 'edit'])->name('prempdr.edit');
        Route::patch('/update/{id}', [PreMpdrController::class, 'update'])->name('prempdr.update');
        Route::delete('/destroy/{id}', [PreMpdrController::class, 'destroy'])->name('prempdr.destroy');
        Route::get('/report', [PreMpdrController::class, 'report'])->name('prempdr.report');
        Route::get('/approval', [PreMpdrController::class, 'approval'])->name('prempdr.approval');
        Route::get('/log', [PreMpdrController::class, 'log'])->name('prempdr.log');
        Route::POST('/store', [PreMpdrController::class, 'store'])->name('prempdr.store');
        Route::get('/form-{no_reg}', [PreMpdrController::class, 'show'])->name('prempdr.form');

        Route::get('/no_reg', [PreMpdrController::class, 'noReg'])->name('prempdr.noReg');

        Route::get('/getFormList', [PreMpdrController::class, 'getFormList'])->name('prempdr.form.list');
        Route::get('/getFormData', [PreMpdrController::class, 'getFormData'])->name('prempdr.form.data');
        Route::get('/getReportData', [PreMpdrController::class, 'getReportData'])->name('prempdr.report.data');


        Route::get('/approval-form-{no_reg}', [PreMpdrApprovalController::class, 'viewApprovalForm'])->name('prempdr.approval.form.view');
        Route::POST('/approve-form-{no_reg}', [PreMpdrApprovalController::class, 'approveForm'])->name('prempdr.form.approve');
        Route::get('/getApprovalListData', [PreMpdrApprovalController::class, 'getApprovalListData'])->name('prempdr.approval.list.data');
        Route::get('/getApprovalFormData', [PreMpdrApprovalController::class, 'getApprovalFormData'])->name('prempdr.approval.form.data');
        Route::get('/getApproverAvailableLevels', [PreMpdrApprovalController::class, 'getApproverAvailableLevels'])->name('prempdr.approver.available.levels');
        Route::delete('/approver/destroy/{nik}', [PreMpdrApprovalController::class, 'destroy'])->name('prempdr.approver.destroy');


        Route::get('/approver', [PreMpdrApprovalController::class, 'approver'])->name('prempdr.approver');
        Route::get('/getSelectedApproverList', [PreMpdrApprovalController::class, 'getSelectedApproverList'])->name('prempdr.selected.approver.list');
        Route::POST('/updateApproverOrder', [PreMpdrApprovalController::class, 'updateApproverOrder'])->name('prempdr.approver.update.order');
        Route::get('/getApproverListData', [PreMpdrApprovalController::class, 'getApproverListData'])->name('prempdr.approver.list.data');


        // Route::get('/mail-approve', [PreMpdrApprovalController::class, 'approveNotReview'])->name('prempdr.approveNotReview');
        // Route::get('/mail-approve-with-review', [PreMpdrApprovalController::class, 'approveWithReview'])->name('prempdr.approveWithReview');
        // Route::get('/mail-not-approve', [PreMpdrApprovalController::class, 'notApprove'])->name('prempdr.notApprove');
        // Route::POST('/mail-comment', [PreMpdrApprovalController::class, 'mailComment'])->name('prempdr.comment');
    });



    // =============================================================MPDR=================================================================
    Route::prefix('mpdr')->group(function () {
        Route::get('/', [MpdrController::class, 'index'])->name('mpdr.index');
        Route::get('/create', [MpdrController::class, 'create'])->name('mpdr.create');
        Route::get('/edit/{id}', [MpdrController::class, 'edit'])->name('mpdr.edit');
        Route::patch('/update/{no_reg}', [MpdrController::class, 'update'])->name('mpdr.update');
        Route::delete('/destroy/{no_reg}', [MpdrController::class, 'destroy'])->name('mpdr.destroy');
        Route::get('/report', [MpdrController::class, 'report'])->name('mpdr.reports');
        Route::get('/log', [MpdrController::class, 'log'])->name('mpdr.log');
        Route::POST('/store', [MpdrController::class, 'store'])->name('mpdr.store');
        Route::get('/form', [MpdrController::class, 'viewForm'])->name('mpdr.form.view');

        Route::get('/no_reg', [MpdrController::class, 'noReg'])->name('mpdr.noReg');

        Route::get('/getFormList', [MpdrController::class, 'getFormList'])->name('mpdr.form.list');
        Route::get('/getFormData', [MpdrController::class, 'getFormData'])->name('mpdr.form.data');
        Route::get('/getReportData', [MpdrController::class, 'getReportData'])->name('mpdr.report.data');


        Route::get('/form-{no_reg}', [MpdrController::class, 'show'])->name('mpdr.form');


        Route::get('/getInitiatorList', [MpdrInitiatorController::class, 'getInitiatorList'])->name('mpdr.initiator.list.data');

        Route::POST('/approve-form-{no_reg}', [MpdrApprovalController::class, 'approveForm'])->name('mpdr.form.approve');
        Route::get('/getApprovalListData', [MpdrApprovalController::class, 'getApprovalListData'])->name('mpdr.approval.list.data');
        Route::POST('/updateApproverOrder', [MpdrApprovalController::class, 'updateApproverOrder'])->name('mpdr.approver.update.order');
        Route::get('/getSelectedApproverList', [MpdrApprovalController::class, 'getSelectedApproverList'])->name('mpdr.selected.approver.list');
        Route::get('/getApproverListData', [MpdrApprovalController::class, 'getApproverListData'])->name('mpdr.approver.list.data');
        Route::get('/approval-form-{no_reg}', [MpdrApprovalController::class, 'viewApprovalForm'])->name('mpdr.approval.form.view');
        Route::get('/approval', [MpdrApprovalController::class, 'approval'])->name('mpdr.approval');
        Route::get('/approver', [MpdrApprovalController::class, 'approver'])->name('mpdr.approver');

        // Route::get('/mail-approve', [MpdrApprovalController::class, 'approveNotReview'])->name('mpdr.approveNotReview');
        // Route::get('/mail-approve-with-review', [MpdrApprovalController::class, 'approveWithReview'])->name('mpdr.approveWithReview');
        // Route::get('/mail-not-approve', [MpdrApprovalController::class, 'notApprove'])->name('mpdr.notApprove');
        // Route::POST('/mail-comment', [MpdrApprovalController::class, 'mailComment'])->name('mpdr.comment');

        Route::POST('/sendMailToGMUrgent/{no_reg}', [MpdrApprovalController::class, 'sendMailToGMUrgent'])->name('mpdr.send.mail.gm');
    });


});


Route::group(['middleware' => ['role:super-admin|admin']], function() {

    Route::resource('permissions', PermissionController::class);
    Route::get('permissions/{permissionId}/delete', [PermissionController::class, 'destroy']);

    Route::resource('roles', RoleController::class);
    Route::get('roles/{roleId}/delete', [RoleController::class, 'destroy']);
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.give-permissions');
    Route::put('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole']);

    Route::resource('users', UserController::class);
    Route::get('getUsersData', [UserController::class, 'getUsersData'])->name('get.users.data');
    Route::get('users/{userId}/delete', [UserController::class, 'destroy']);

});



require __DIR__.'/auth.php';
