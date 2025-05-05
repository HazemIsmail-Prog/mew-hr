@php
    use App\Models\Department;
    use App\Models\User;
    use App\Models\Mission;
    use App\Models\Permission;
    use App\Models\Request;
    use App\Models\Exemption;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class=" flex items-center space-x-2" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group class="grid">
                    <!-- <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item> -->
                    @can('viewAny', Department::class)
                        <flux:navlist.item icon="users" :href="route('departments.index')" :current="request()->routeIs('departments.*')" wire:navigate>{{ __('Departments') }}</flux:navlist.item>
                    @endcan
                    @can('viewAny', User::class)
                        <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    @endcan
                    @can('viewAny', Mission::class)
                        <flux:navlist.item icon="users" :href="route('missions.index')" :current="request()->routeIs('missions.*')" wire:navigate>{{ __('Missions') }}</flux:navlist.item>
                    @endcan
                    @can('viewAny', Permission::class)
                        <flux:navlist.item icon="users" :href="route('permissions.index')" :current="request()->routeIs('permissions.*')" wire:navigate>{{ __('Permissions') }}</flux:navlist.item>
                    @endcan
                    @can('viewAny', Exemption::class)
                        <flux:navlist.item icon="users" :href="route('exemptions.index')" :current="request()->routeIs('exemptions.*')" wire:navigate>{{ __('Exemptions') }}</flux:navlist.item>
                    @endcan

                </flux:navlist.group>
                @can('viewAny', Request::class)
                <flux:navlist.group
                    heading="{{ __('Requests') }}"
                    x-data="{
                        pendingMissions: 0,
                        pendingPermissions: 0, 
                        pendingExemptions: 0,
                        init() {
                            this.fetchCounts();
                            document.addEventListener('requests-updated', () => {
                                this.fetchCounts();
                            });
                        },
                        fetchCounts() {
                            axios.get('/requests/counts')
                                .then(response => {
                                    this.pendingMissions = response.data.missionsCount;
                                    this.pendingPermissions = response.data.permissionsCount;
                                    this.pendingExemptions = response.data.exemptionsCount;
                                })
                                .catch(error => console.error('Error fetching counts:', error));
                        }
                    }"
                >
                    <flux:navlist.item icon="users" :href="route('requests.missions')" :current="request()->routeIs('requests.missions')" wire:navigate>
                        <div class="flex items-center justify-between gap-2">
                            {{ __('Missions') }}
                            <flux:badge x-show="pendingMissions > 0" color="red" size="sm" class="!text-xs !font-medium !px-1 !py-0.5" x-text="pendingMissions"></flux:badge>
                        </div>
                    </flux:navlist.item>
                    <flux:navlist.item icon="users" :href="route('requests.permissions')" :current="request()->routeIs('requests.permissions')" wire:navigate>
                        <div class="flex items-center justify-between gap-2">
                            {{ __('Permissions') }}
                            <flux:badge x-show="pendingPermissions > 0" color="red" size="sm" class="!text-xs !font-medium !px-1 !py-0.5" x-text="pendingPermissions"></flux:badge>
                        </div>
                    </flux:navlist.item>
                    <flux:navlist.item icon="users" :href="route('requests.exemptions')" :current="request()->routeIs('requests.exemptions')" wire:navigate>
                        <div class="flex items-center justify-between gap-2">
                            {{ __('Exemptions') }}
                            <flux:badge x-show="pendingExemptions > 0" color="red" size="sm" class="!text-xs !font-medium !px-1 !py-0.5" x-text="pendingExemptions"></flux:badge>
                        </div>
                    </flux:navlist.item>
                </flux:navlist.group>
                @endcan
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->cid }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
