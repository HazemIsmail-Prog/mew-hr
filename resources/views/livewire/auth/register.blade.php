<?php

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public $department_id = '';
    public $supervisor_id = '';
    public $replacement_id = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $cid = '';
    public string $role = '';
    public string $file_number = '';
    public $departments;
    public $supervisors;
    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'cid' => ['required', 'string', 'max:12', 'min:12', 'unique:' . User::class],
            'file_number' => ['required', 'string', 'max:12', 'unique:' . User::class],
            'department_id' => ['required', 'numeric', 'exists:departments,id'],
            'supervisor_id' => ['required_if:role,employee', 'exists:users,id'],
            'role' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        if ($validated['role'] == 'supervisor') {
            $validated['supervisor_id'] = null;
        }

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }

    public function mount()
    {
        $this->departments = Department::all();
        $this->supervisors = User::where('role', 'supervisor')->get()->pluck('name','id');
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Full name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('name in Arabic')"
        />

        <!-- Civil ID -->
        <flux:input
            wire:model.number="cid"
            :label="__('Civil ID')"
            type="number"
            min="100000000000"
            max="999999999999"
            required
            autocomplete="cid"
        />

        <!-- File Number -->
        <flux:input
            wire:model="file_number"
            :label="__('File Number')"
            type="number"
            required
        />

                <!-- Department -->
        <flux:select
            wire:model.number="department_id"
            :label="__('Department')"
        >
            <option value="">{{ __('Select Department') }}</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </flux:select>

        <!-- role -->
        <flux:select
            wire:model.live="role"
            :label="__('Role')"
        >
            <option value="">{{ __('Select Role') }}</option>
            <option value="supervisor">{{ __('Manager') }}</option>
            <option value="employee">{{ __('Employee') }}</option>
        </flux:select>



        @if ($role == 'employee')
            <!-- Supervisor -->
            <flux:select
                wire:model="supervisor_id"
            :label="__('Supervisor')"
        >
            <option value="">{{ __('Select Supervisor') }}</option>
            @foreach ($supervisors as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
            </flux:select>
        @endif

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
