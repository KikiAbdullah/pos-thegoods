<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create permissions

        $permissions = [
            'view_permissions',

            'view_roles',
            'edit_roles',

            'view_users',
            'add_users',
            'edit_users',
            'delete_users',

            //MENU KASIR
            'kasir_view',
            //MENU KASIR

            ///POS
            'pos_access',
            'operator_access',
            ///POS

            ///MASTER
            'master_package',
            'master_addon',
            'master_tipe_pembayaran',
            ///MASTER

            ///REPORT
            'report_transaksi'
            ///REPORT
        ];

        $permission_admin = [
            //MENU KASIR
            'kasir_view',
            //MENU KASIR

            ///POS
            'pos_access',
            ///POS

            ///MASTER
            'master_package',
            'master_addon',
            'master_tipe_pembayaran',
            ///MASTER

            ///REPORT
            'report_transaksi'
            ///REPORT
        ];

        $permission_operator = [
            ///POS
            'operator_access',
            ///POS
        ];

        foreach ($permissions as  $permission) {
            if (Permission::where('name',  $permission)->count() <= 0) {
                Permission::create(['name' =>  $permission]);
            }
        }


        // create roles and assign existing permissions
        $roleSuperAdmin = Role::create(['name' => 'SUPERADMIN']);

        $roleAdmin = Role::create(['name' => 'ADMIN']);
        foreach ($permission_admin as $admin) {
            $roleAdmin->givePermissionTo($admin);
        }

        $roleOperator = Role::create(['name' => 'OPERATOR']);
        foreach ($permission_operator as $operator) {
            $roleOperator->givePermissionTo($operator);
        }

        $user = \App\User::find(1);
        $user->assignRole($roleSuperAdmin);

        $userOperator = \App\User::find(2);
        $userOperator->assignRole($roleOperator);

        $userAdmin1 = \App\User::find(3);
        $userAdmin1->assignRole($roleAdmin);

        $userAdmin2 = \App\User::find(4);
        $userAdmin2->assignRole($roleAdmin);
    }
}
