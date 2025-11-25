<?php
namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{

    public function run(): void {
        $role = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'api',
        ]);

        $perms = [
            'book.create',
            'book.update',
            'book.view',
            'book.delete',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate([
                'name'       => $p,
                'guard_name' => 'api',
            ]);
        }

        $role->syncPermissions($perms);

        $user = User::where('email', 'admin@example.com')->first();

        if ($user) {
            $user->syncRoles([$role->name]);
        }
    }
}
