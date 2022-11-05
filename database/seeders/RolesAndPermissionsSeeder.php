<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        // Create permissions for products
        $addProduct = "add product";
        $editProduct = "edit product";
        $deleteProduct = "delete product";
        $viewProduct = "view product";

        // Create permissions for users
        $viewUsers = "view users";
        $addUsers = "add users";
        $editUsers = "edit users";
        $deleteUsers = "delete users";


        // Manage product permissions
        Permission::create(['name' => $addProduct]);
        Permission::create(['name' => $editProduct]);
        Permission::create(['name' => $deleteProduct]);
        Permission::create(['name' => $viewProduct]);

        // Manage user permissions
        Permission::create(['name' => $viewUsers]);
        Permission::create(['name' => $addUsers]);
        Permission::create(['name' => $editUsers]);
        Permission::create(['name' => $deleteUsers]);

        // Define rules available
        $admin = "admin";
        $seller = "seller";
        $costumer = "costumer";

        // Create roles and assign created permissions
        Role::create(['name' => $admin])->givePermissionTo(Permission::all());

        Role::create(['name' => $seller])->givePermissionTo([
            $addProduct,
            $editProduct,
            $deleteProduct,
            $viewProduct
        ]);

        Role::create(['name' => $costumer])->givePermissionTo([
            $viewProduct,
        ]);
    }
}
