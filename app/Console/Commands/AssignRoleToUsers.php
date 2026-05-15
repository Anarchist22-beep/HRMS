<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRoleToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:role {role} {user_ids*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to given user IDs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->argument('role');
        $userIds = $this->argument('user_ids');

        //  Fetch role with correct guard
        $role = Role::where('name', $roleName)
            ->where('guard_name', 'api')
            ->first();

        if (!$role) {
            $this->error("Role '{$roleName}' not found for guard 'api'!");
            return;
        }

        foreach ($userIds as $id) {
            $user = User::find($id);

            if (!$user) {
                $this->warn("User with ID {$id} not found.");
                continue;
            }

            //  Assign role using role instance (avoids guard issues)
            $user->syncRoles([$role]);

            //  ALSO store in users table (if you insist)
            $user->role = $roleName;
            $user->save();

            $this->info("Assigned {$roleName} role to User ID {$id}");
        }

        $this->info('Done!');
    }
}
