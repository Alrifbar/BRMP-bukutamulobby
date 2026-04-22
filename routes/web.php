<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\FormBuilderController;
use App\Http\Controllers\PengunjungController;
use Illuminate\Support\Facades\Route;

// Dynamic root route - always redirect to buku tamu form
Route::get('/', function () {
    return redirect()->route('buku-tamu.create');
});

// Admin routes
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::get('/admin/register', [AuthController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected admin routes
Route::middleware(['admin.auth', 'auto.backup'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/verify', [AuthController::class, 'verify'])->name('admin.verify');
    
    // Grafik dengan filter tanggal
    Route::get('/admin/grafik', [DashboardController::class, 'grafik'])->name('admin.grafik');
    Route::get('/admin/grafik/data', [DashboardController::class, 'grafikData'])->name('admin.grafik.data');
    Route::post('/admin/grafik/filter', [DashboardController::class, 'grafik'])->name('admin.grafik.filter');
    Route::post('/admin/grafik/export-all-pdf', [DashboardController::class, 'exportAllChartsPdf'])->name('admin.grafik.export-all-pdf');
    Route::post('/admin/grafik/export-chart-pdf', [DashboardController::class, 'exportChartPdf'])->name('admin.grafik.export-chart-pdf');
    Route::get('/admin/grafik/export-chart-excel', [DashboardController::class, 'exportChartExcel'])->name('admin.grafik.export-chart-excel');
    
    Route::get('/admin/rekap-pengunjung', [RekapController::class, 'rekapPengunjung'])->name('admin.rekap-pengunjung');
    Route::get('/admin/pengunjung', [RekapController::class, 'indexPengunjung'])->name('admin.pengunjung.index');

    // Form Builder routes
    Route::get('/admin/form-builder', [FormBuilderController::class, 'index'])->name('admin.form-builder.index');
    Route::post('/admin/form-builder', [FormBuilderController::class, 'store'])->name('admin.form-builder.store');
    Route::put('/admin/form-builder/{formField}', [FormBuilderController::class, 'update'])->name('admin.form-builder.update');
    Route::delete('/admin/form-builder/{formField}', [FormBuilderController::class, 'destroy'])->name('admin.form-builder.destroy');
    Route::post('/admin/form-builder/reorder', [FormBuilderController::class, 'reorder'])->name('admin.form-builder.reorder');

    // Export routes
    Route::get('/admin/grafik/export-all-excel', [DashboardController::class, 'exportAllExcel'])->name('admin.grafik.export-all-excel');
    Route::get('/admin/export/excel/{year}', [RekapController::class, 'exportExcel'])->name('admin.export.excel');
    Route::get('/admin/export/pdf/{year}', [RekapController::class, 'exportPdf'])->name('admin.export.pdf');
    // Export Grafik Harian
    Route::get('/admin/grafik/export-harian', [DashboardController::class, 'exportGrafikHarian'])->name('admin.grafik.export-harian');

    // Monthly export routes
    Route::get('/admin/export/excel/{year}/{month}', [RekapController::class, 'exportExcel'])->name('admin.export.month.excel');
    Route::get('/admin/export/pdf/{year}/{month}', [RekapController::class, 'exportPdf'])->name('admin.export.month.pdf');

    // Import Excel route
    Route::post('/admin/import/excel', [RekapController::class, 'importExcel'])->name('admin.import.excel');

    // Delete data route
    Route::post('/admin/delete/data', [RekapController::class, 'deleteData'])->name('admin.delete.data');
    // Trash management
    Route::post('/admin/trash/restore', [SettingsController::class, 'restoreTrash'])->name('admin.trash.restore');
    Route::post('/admin/trash/purge', [SettingsController::class, 'purgeTrash'])->name('admin.trash.purge');

    Route::get('/admin/pengunjung/{pengunjung}/edit', [RekapController::class, 'editPengunjung'])->name('admin.pengunjung.edit');
    Route::put('/admin/pengunjung/{pengunjung}', [RekapController::class, 'updatePengunjung'])->name('admin.pengunjung.update');
    Route::delete('/admin/pengunjung/{pengunjung}', [RekapController::class, 'destroyPengunjung'])->name('admin.pengunjung.destroy');
    
    // Bulk operations routes
    Route::delete('/admin/pengunjung/bulk-delete', [RekapController::class, 'bulkDeletePengunjung'])->name('admin.pengunjung.bulk-delete');
    Route::post('/admin/pengunjung/bulk-export', [RekapController::class, 'bulkExportPengunjung'])->name('admin.pengunjung.bulk-export');
    
    // Gender routes
    Route::put('/admin/pengunjung/{pengunjung}/gender', [RekapController::class, 'updateGender'])->name('admin.pengunjung.update.gender');
    Route::get('/admin/gender-data', [RekapController::class, 'getGenderData'])->name('admin.gender.data');
    

    // Settings routes
    Route::get('/admin/pengaturan', [SettingsController::class, 'showLogoSettings'])->name('admin.pengaturan');
    Route::put('/admin/pengaturan/logo', [SettingsController::class, 'updateLogo'])->name('admin.pengaturan.logo.update');
    Route::put('/admin/pengaturan/login-text', [SettingsController::class, 'updateLoginText'])->name('admin.pengaturan.login.text.update');
    Route::put('/admin/pengaturan/buku-tamu-text', [SettingsController::class, 'updateBukuTamuText'])->name('admin.pengaturan.buku-tamu.text.update');
    Route::put('/admin/pengaturan/theme', [SettingsController::class, 'updateThemeColors'])->name('admin.pengaturan.theme.update');
    Route::post('/admin/pengaturan/reset-all', [SettingsController::class, 'resetAllSettings'])->name('admin.pengaturan.reset-all');
    // Backup routes
    Route::post('/admin/backup-now', [SettingsController::class, 'backupNow'])->name('admin.backup.now');
    Route::post('/admin/update-backup-settings', [SettingsController::class, 'updateBackupSettings'])->name('admin.backup.update-settings');
    Route::post('/admin/full-backup-now', [SettingsController::class, 'fullBackupNow'])->name('admin.backup.full-now');
    Route::post('/admin/test-drive-write', [SettingsController::class, 'testDriveWrite'])->name('admin.test-drive-write');
    Route::post('/admin/test-local-write', [SettingsController::class, 'testLocalWrite'])->name('admin.test-local-write');
    Route::get('/admin/download-sql', [SettingsController::class, 'downloadSql'])->name('admin.download-sql');
    Route::post('/admin/restore-sql', [SettingsController::class, 'restoreSql'])->name('admin.restore-sql');
    Route::post('/admin/schedule-sql-download', [SettingsController::class, 'scheduleSqlDownload'])->name('admin.schedule-sql-download');
});

Route::get('/buku-tamu', [PengunjungController::class, 'create'])->name('buku-tamu.create');
Route::post('/buku-tamu', [PengunjungController::class, 'store'])->name('buku-tamu.store');
Route::get('/buku-tamu/terima-kasih', [PengunjungController::class, 'terimaKasih'])->name('buku-tamu.terima-kasih');
Route::get('/buku-tamu/edit/{token}', [PengunjungController::class, 'edit'])->name('buku-tamu.edit');
Route::put('/buku-tamu/update/{token}', [PengunjungController::class, 'update'])->name('buku-tamu.update');

// API for month details (AJAX)
Route::get('/admin/pengunjung/api/month-details', [RekapController::class, 'getMonthDetailsApi'])->name('admin.pengunjung.api.month-details');
