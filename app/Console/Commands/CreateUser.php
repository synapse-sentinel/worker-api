<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {--name=} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userAttributes = [
            'name' => $this->option('name'),
            'email' => $this->option('email'),
        ];

        $providedParams = $this->askForMissingParams($userAttributes);

        // Create a new user using the Fortify action
        $newUser = (new CreateNewUser)->create([
            'name' => $providedParams['name'],
            'email' => $providedParams['email'],
            'password' => $providedParams['password'],
            'password_confirmation' => $providedParams['password'],
        ]);

        table([
            ['ID', 'Name', 'Email', 'Created At', 'Updated At'],
            [$newUser->id, $newUser->name, $newUser->email, $newUser->created_at, $newUser->updated_at],
        ]);

        $this->info('User created successfully.');
    }

    /**
     * Ask the user for any missing parameters.
     */
    private function askForMissingParams(array $userAttributes): array
    {
        $userAttributes['name'] = text(
            label: 'Enter the user\'s name:',
            default: $userAttributes['name'],
            validate: 'required|string|max:255');

        $userAttributes['email'] = text(label: 'Enter the user\'s email:',
            default: $userAttributes['email'] != null ? $userAttributes['email'] : '',
            validate: 'required|email|max:255|unique:users,email');

        $userAttributes['password'] = password(
            label: 'Enter the user\'s password:',
            validate: 'required|string|min:8');

        return $userAttributes;
    }
}
