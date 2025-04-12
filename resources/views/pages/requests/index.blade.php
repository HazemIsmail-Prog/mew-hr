<x-layouts.app :title="__('Requests')">
    <div x-data="requestsComponent()" x-on:refresh-counters.window="getCounts()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- <h1>{{__('Requests')}}</h1> -->

        <div class="flex border-b">
            <button 
                @click="activeTab = 'permissions'" 
                :class="{ 'border-b-2 border-blue-500': activeTab === 'permissions' }"
                class="px-4 py-2 font-medium">
                {{ __('Permissions') }}
                <span class="text-sm text-gray-500" x-text="permissionsCount"></span>
            </button>
            <button 
                @click="activeTab = 'missions'"
                :class="{ 'border-b-2 border-blue-500': activeTab === 'missions' }"
                class="px-4 py-2 font-medium">
                {{ __('Missions') }}
                <span class="text-sm text-gray-500" x-text="missionsCount"></span>
            </button>
        </div>
        
        <template x-if="activeTab === 'permissions'" class="mt-4">
            <div>
                @include('pages.requests.permissions')
            </div>
        </template>
        <template x-if="activeTab === 'missions'" class="mt-4">
            <div>
                @include('pages.requests.missions')
            </div>
        </template>
    </div>

    <script>
        function requestsComponent() {
            return {
                activeTab: 'missions',
                permissionsCount: 0,
                missionsCount: 0,

                init() {
                    this.getCounts();
                },

                getCounts() {
                    axios.get('/requests/counts').then(response => {
                        this.permissionsCount = response.data.permissionsCount;
                        this.missionsCount = response.data.missionsCount;
                    });
                },

            };
        }
    </script>
</x-layouts.app>
