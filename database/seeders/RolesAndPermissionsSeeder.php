<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        Permission::create(['name' => 'create-post']);
        Permission::create(['name' => 'edit-post']);

        // Create roles and assign permissions
        $editor = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $writer = Role::create(['name' => 'writer', 'guard_name' => 'web']);

        $editor->givePermissionTo(['create-post', 'edit-post']);
        $writer->givePermissionTo('create-post');
    }
}
