<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = [
            'dashboard.view',

            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            'vendor.view',
            'vendor.create',
            'vendor.update',
            'vendor.delete',

            'permintaan-pembelian.view',
            'permintaan-pembelian.create',
            'permintaan-pembelian.update',
            'permintaan-pembelian.delete',
            'permintaan-pembelian.print',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            'roles.manage',

            'pesanan-pembelian.view',
            'pesanan-pembelian.create',
            'pesanan-pembelian.update',
            'pesanan-pembelian.delete',

            'penerimaan-barang.view',
            'penerimaan-barang.create',
            'penerimaan-barang.update',
            'penerimaan-barang.delete',

            'faktur-pembelian.view',
            'faktur-pembelian.create',
            'faktur-pembelian.update',
            'faktur-pembelian.delete',

            'pembayaran-pembelian.view',
            'pembayaran-pembelian.create',
            'pembayaran-pembelian.update',
            'pembayaran-pembelian.delete',

            'retur-pembelian.view',
            'retur-pembelian.create',
            'retur-pembelian.update',
            'retur-pembelian.delete',

            'pesanan-penjualan.view',
            'pesanan-penjualan.create',
            'pesanan-penjualan.update',
            'pesanan-penjualan.delete',

            'pengiriman-pesanan.view',
            'pengiriman-pesanan.create',
            'pengiriman-pesanan.update',
            'pengiriman-pesanan.delete',
        ];

        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->syncPermissions($allPermissions);

        $staffRole = Role::findOrCreate('Staff', 'web');

        $staffRole->syncPermissions([
            'dashboard.view',

            'barang.view',
            'barang.create',
            'barang.update',

            'vendor.view',
            'vendor.create',
            'vendor.update',

            'permintaan-pembelian.view',
            'permintaan-pembelian.create',
            'permintaan-pembelian.update',
            'permintaan-pembelian.print',

            'pesanan-pembelian.view',
            'pesanan-pembelian.create',
            'pesanan-pembelian.update',

            'penerimaan-barang.view',
            'penerimaan-barang.create',
            'penerimaan-barang.update',

            'faktur-pembelian.view',
            'faktur-pembelian.create',
            'faktur-pembelian.update',

            'pembayaran-pembelian.view',
            'pembayaran-pembelian.create',
            'pembayaran-pembelian.update',

            'retur-pembelian.view',
            'retur-pembelian.create',
            'retur-pembelian.update',

            'pesanan-penjualan.view',
            'pesanan-penjualan.create',
            'pesanan-penjualan.update',

            'pengiriman-pesanan.view',
            'pengiriman-pesanan.create',
            'pengiriman-pesanan.update',
        ]);

        // Berikan role Admin kepada akun administrator yang sudah dibuat.
        $admin = User::where('email', 'admin@gmail.com')->first();

        if ($admin && ! $admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }
    }
}