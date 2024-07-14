<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user or promote an existing user to admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->is_admin = true;
            $user->save();
            $this->info("User {$email} has been promoted to admin.");
        } else {
            $this->error("User not found. Please register the user first.");
        }
    }
}
