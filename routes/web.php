<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MBarangController;
use App\Http\Controllers\MVendorController;
use App\Http\Controllers\TPermintaanPembelianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PesananPembelianController;
use App\Http\Controllers\PenerimaanBarangController;
use App\Http\Controllers\FakturPembelianController;
use App\Http\Controllers\PembayaranPembelianController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', fn () => view('dashboard'))
        ->middleware('can:dashboard.view')
        ->name('dashboard');

    // Master Barang
    Route::get('/barang', [MBarangController::class, 'index'])
        ->middleware('can:barang.view')
        ->name('barang.index');

    Route::get('/barang/create', [MBarangController::class, 'create'])
        ->middleware('can:barang.create')
        ->name('barang.create');

    Route::post('/barang', [MBarangController::class, 'store'])
        ->middleware('can:barang.create')
        ->name('barang.store');

    Route::get('/barang/{sku}/edit', [MBarangController::class, 'edit'])
        ->middleware('can:barang.update')
        ->name('barang.edit');

    Route::put('/barang/{sku}', [MBarangController::class, 'update'])
        ->middleware('can:barang.update')
        ->name('barang.update');

    Route::delete('/barang/{sku}', [MBarangController::class, 'destroy'])
        ->middleware('can:barang.delete')
        ->name('barang.destroy');

    // Master Vendor
    Route::get('/vendor', [MVendorController::class, 'index'])
        ->middleware('can:vendor.view')
        ->name('vendor.index');

    Route::get('/vendor/create', [MVendorController::class, 'create'])
        ->middleware('can:vendor.create')
        ->name('vendor.create');

    Route::post('/vendor', [MVendorController::class, 'store'])
        ->middleware('can:vendor.create')
        ->name('vendor.store');

    Route::get('/vendor/{vendor}/edit', [MVendorController::class, 'edit'])
        ->middleware('can:vendor.update')
        ->name('vendor.edit');

    Route::put('/vendor/{vendor}', [MVendorController::class, 'update'])
        ->middleware('can:vendor.update')
        ->name('vendor.update');

    Route::delete('/vendor/{vendor}', [MVendorController::class, 'destroy'])
        ->middleware('can:vendor.delete')
        ->name('vendor.destroy');

    // Permintaan Pembelian
    Route::get(
        '/permintaan-pembelian',
        [TPermintaanPembelianController::class, 'index']
    )->middleware('can:permintaan-pembelian.view')
        ->name('permintaan-pembelian.index');

    Route::get(
        '/permintaan-pembelian/create',
        [TPermintaanPembelianController::class, 'create']
    )->middleware('can:permintaan-pembelian.create')
        ->name('permintaan-pembelian.create');

    Route::post(
        '/permintaan-pembelian',
        [TPermintaanPembelianController::class, 'store']
    )->middleware('can:permintaan-pembelian.create')
        ->name('permintaan-pembelian.store');

    Route::get(
        '/permintaan-pembelian/{id}/cetak',
        [TPermintaanPembelianController::class, 'cetak']
    )->middleware('can:permintaan-pembelian.print')
        ->name('permintaan-pembelian.print');

    Route::get(
        '/permintaan-pembelian/{id}/edit',
        [TPermintaanPembelianController::class, 'edit']
    )->middleware('can:permintaan-pembelian.update')
        ->name('permintaan-pembelian.edit');

    Route::put(
        '/permintaan-pembelian/{id}',
        [TPermintaanPembelianController::class, 'update']
    )->middleware('can:permintaan-pembelian.update')
        ->name('permintaan-pembelian.update');

    Route::delete(
        '/permintaan-pembelian/{id}',
        [TPermintaanPembelianController::class, 'destroy']
    )->middleware('can:permintaan-pembelian.delete')
        ->name('permintaan-pembelian.destroy');

    // User Management
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('can:users.view')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware(['can:users.create', 'can:roles.manage'])
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware(['can:users.create', 'can:roles.manage'])
        ->name('users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware(['can:users.update', 'can:roles.manage'])
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware(['can:users.update', 'can:roles.manage'])
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('can:users.delete')
        ->name('users.destroy');

    // Role & Permission Management
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('can:roles.manage')
        ->name('roles.index');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('can:roles.manage')
        ->name('roles.edit');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('can:roles.manage')
        ->name('roles.update');

    // Pesanan Pembelian
    Route::get('/pesanan-pembelian', [
        PesananPembelianController::class,
        'index',
    ])
        ->middleware('can:pesanan-pembelian.view')
        ->name('pesanan-pembelian.index');

    Route::get('/pesanan-pembelian/create', [
        PesananPembelianController::class,
        'create',
    ])
        ->middleware('can:pesanan-pembelian.create')
        ->name('pesanan-pembelian.create');

    Route::post('/pesanan-pembelian', [
        PesananPembelianController::class,
        'store',
    ])
        ->middleware('can:pesanan-pembelian.create')
        ->name('pesanan-pembelian.store');

    Route::get('/pesanan-pembelian/{pesanan_pembelian}/edit', [
        PesananPembelianController::class,
        'edit',
    ])
        ->middleware('can:pesanan-pembelian.update')
        ->name('pesanan-pembelian.edit');

    Route::put('/pesanan-pembelian/{pesanan_pembelian}', [
        PesananPembelianController::class,
        'update',
    ])
        ->middleware('can:pesanan-pembelian.update')
        ->name('pesanan-pembelian.update');

    Route::get(
        '/pesanan-pembelian/{pesanan_pembelian}/cetak',
        [PesananPembelianController::class, 'cetak']
    )
        ->middleware('can:pesanan-pembelian.view')
        ->name('pesanan-pembelian.print');
    
    Route::delete('/pesanan-pembelian/{pesanan_pembelian}', [
        PesananPembelianController::class,
        'destroy',
    ])
        ->middleware('can:pesanan-pembelian.delete')
        ->name('pesanan-pembelian.destroy');

    // Penerimaan Barang
    Route::get(
        '/penerimaan-barang',
        [PenerimaanBarangController::class, 'index']
    )->middleware('can:penerimaan-barang.view')
        ->name('penerimaan-barang.index');

    Route::get(
        '/penerimaan-barang/create',
        [PenerimaanBarangController::class, 'create']
    )->middleware('can:penerimaan-barang.create')
        ->name('penerimaan-barang.create');

    Route::post(
        '/penerimaan-barang',
        [PenerimaanBarangController::class, 'store']
    )->middleware('can:penerimaan-barang.create')
        ->name('penerimaan-barang.store');

    Route::get(
        '/penerimaan-barang/{no_penerimaan}/cetak',
        [PenerimaanBarangController::class, 'cetak']
    )->middleware('can:penerimaan-barang.view')
        ->name('penerimaan-barang.print');

    Route::get(
        '/penerimaan-barang/{no_penerimaan}/edit',
        [PenerimaanBarangController::class, 'edit']
    )->middleware('can:penerimaan-barang.update')
        ->name('penerimaan-barang.edit');

    Route::put(
        '/penerimaan-barang/{no_penerimaan}',
        [PenerimaanBarangController::class, 'update']
    )->middleware('can:penerimaan-barang.update')
        ->name('penerimaan-barang.update');

    Route::delete(
        '/penerimaan-barang/{no_penerimaan}',
        [PenerimaanBarangController::class, 'destroy']
    )->middleware('can:penerimaan-barang.delete')
        ->name('penerimaan-barang.destroy');

    // Faktur Pembelian
    Route::get(
        '/faktur-pembelian',
        [FakturPembelianController::class, 'index']
    )->middleware('can:faktur-pembelian.view')
        ->name('faktur-pembelian.index');

    Route::get(
        '/faktur-pembelian/create',
        [FakturPembelianController::class, 'create']
    )->middleware('can:faktur-pembelian.create')
        ->name('faktur-pembelian.create');

    Route::post(
        '/faktur-pembelian',
        [FakturPembelianController::class, 'store']
    )->middleware('can:faktur-pembelian.create')
        ->name('faktur-pembelian.store');

    Route::get(
        '/faktur-pembelian/{no_faktur}/cetak',
        [FakturPembelianController::class, 'cetak']
    )->middleware('can:faktur-pembelian.view')
        ->name('faktur-pembelian.cetak');

    Route::get(
        '/faktur-pembelian/{no_faktur}/edit',
        [FakturPembelianController::class, 'edit']
    )->middleware('can:faktur-pembelian.update')
        ->name('faktur-pembelian.edit');

    Route::put(
        '/faktur-pembelian/{no_faktur}',
        [FakturPembelianController::class, 'update']
    )->middleware('can:faktur-pembelian.update')
        ->name('faktur-pembelian.update');

    Route::delete(
        '/faktur-pembelian/{no_faktur}',
        [FakturPembelianController::class, 'destroy']
    )->middleware('can:faktur-pembelian.delete')
        ->name('faktur-pembelian.destroy');

    // Pembayaran Pembelian
    Route::get(
        '/pembayaran-pembelian',
        [PembayaranPembelianController::class, 'index']
    )->middleware('can:pembayaran-pembelian.view')
        ->name('pembayaran-pembelian.index');

    Route::get(
        '/pembayaran-pembelian/create',
        [PembayaranPembelianController::class, 'create']
    )->middleware('can:pembayaran-pembelian.create')
        ->name('pembayaran-pembelian.create');

    Route::post(
        '/pembayaran-pembelian',
        [PembayaranPembelianController::class, 'store']
    )->middleware('can:pembayaran-pembelian.create')
        ->name('pembayaran-pembelian.store');

    Route::get(
        '/pembayaran-pembelian/{no_pembayaran}/cetak',
        [PembayaranPembelianController::class, 'cetak']
    )->middleware('can:pembayaran-pembelian.view')
        ->name('pembayaran-pembelian.cetak');

    Route::get(
        '/pembayaran-pembelian/{no_pembayaran}/edit',
        [PembayaranPembelianController::class, 'edit']
    )->middleware('can:pembayaran-pembelian.update')
        ->name('pembayaran-pembelian.edit');

    Route::put(
        '/pembayaran-pembelian/{no_pembayaran}',
        [PembayaranPembelianController::class, 'update']
    )->middleware('can:pembayaran-pembelian.update')
        ->name('pembayaran-pembelian.update');

    Route::delete(
        '/pembayaran-pembelian/{no_pembayaran}',
        [PembayaranPembelianController::class, 'destroy']
    )->middleware('can:pembayaran-pembelian.delete')
        ->name('pembayaran-pembelian.destroy');
});