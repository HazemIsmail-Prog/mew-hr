<div x-data="permissionsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg dark:border-gray-700">
        <flux:select x-model="filters.user">
            <option value="">{{__('Select Employee')}}</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </flux:select>
        <flux:select x-model="filters.status">
            <option value="">{{__('All Status')}}</option>
            <option value="pending">{{__('Pending')}}</option>
            <option value="approved">{{__('Approved')}}</option>
            <option value="rejected">{{__('Rejected')}}</option>
            <option value="for review">{{__('For Review')}}</option>
        </flux:select>
    </div>

    <template x-if="permissions.length > 0">
        <div class="space-y-4 pb-5">
            <template x-for="permission in permissions" :key="permission.id">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg dark:border-gray-700">
                    <div class="flex-1 flex flex-col lg:flex-row lg:items-center gap-4">
                        <div class="flex-1">
                            <h1 x-text="permission.user.name"></h1>
                            <div class="text-sm font-medium dark:text-gray-200" x-text="permission.date"></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400" x-text="permission.reason"></div>
                            <div x-show="permission.notes" class="text-sm text-gray-500 dark:text-gray-400" x-text="permission.notes"></div>
                            <span x-show="permission.type" class="px-2 py-1 text-xs font-medium rounded-full" 
                                x-bind:class="{
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': permission.type === 'in',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': permission.type === 'out',
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': permission.type === 'in-out'
                                }"
                                x-text="permission.translated_type">
                            </span>
                            <span x-show="permission.status" class="px-2 py-1 text-xs font-medium rounded-full"
                                x-bind:class="{
                                    'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200': permission.status === 'pending',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': permission.status === 'approved',
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': permission.status === 'rejected',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': permission.status === 'for review'
                                }"
                                x-text="permission.translated_status">
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2 w-full lg:w-auto">
                        <!-- Loading button that shows when any status change is in progress -->
                        <flux:button x-show="loadingPermissionId === permission.id" variant="primary" disabled>
                            <svg class="animate-spin h-4 w-4 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{__('Processing...')}}
                        </flux:button>
                        
                        <!-- Regular buttons that show when no status change is in progress -->
                        <div x-show="loadingPermissionId !== permission.id" class="flex gap-2 w-full lg:w-auto">
                            <flux:button x-show="permission.status !== 'approved'" variant="primary" x-on:click="approvePermission(permission)" title="{{__('Approve')}}">
                                {{__('Approve')}}
                            </flux:button>
                            <flux:button x-show="permission.status !== 'rejected'" variant="danger" x-on:click="rejectPermission(permission)" title="{{__('Reject')}}">
                                {{__('Reject')}}
                            </flux:button>
                            <flux:button x-show="permission.status !== 'for review'" variant="filled" x-on:click="reviewPermission(permission)" title="{{__('Review')}}">
                                {{__('Review')}}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>
        <!-- non flux Pagination with top shadow -->
    <div x-cloak x-show="meta.last_page > 1" class="flex bg-zinc-50 dark:bg-zinc-900 items-center justify-end gap-2 fixed bottom-0 end-0 start-0 p-2">
        <flux:button class="select-none dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600" size="sm" x-bind:disabled="currentPage == 1" x-on:click="goToPage(currentPage - 1)">{{__('Previous')}}</flux:button>
        <template x-for="link in links" :key="link.label">
            <flux:button class="select-none !hidden lg:!block dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600" size="sm" x-bind:disabled="currentPage == link.label" x-on:click="goToPage(link.label)" x-html="link.label"></flux:button>
        </template>
        <flux:button class="select-none dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600" size="sm" x-bind:disabled="currentPage == meta.last_page" x-on:click="goToPage(currentPage + 1)">{{__('Next')}}</flux:button>
    </div>
</div>

<script>
function permissionsComponent() {
    return {
        permissions: [],
        meta: {},
        links: [],
        currentPage: 1,
        selectedPermission: null,
        loadingPermissionId: null,
        filters: {
            user: '',
            status: 'pending'
        },


        init() {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.getPermissions();
            this.$watch('filters', () => {
                this.getPermissions();
            });
        },

        getPermissions() {
            axios.get('/requests/permissions', {
                params: {
                    page: this.currentPage,
                    user: this.filters.user,
                    status: this.filters.status
                }
            })
            .then(response => {
                const data = response.data;
                this.permissions = data.data;
                this.meta = data.meta;
                this.currentPage = data.meta.current_page;
                this.links = data.meta.links;
                this.links.shift();
                this.links.pop();
            });
        },
        
        goToPage(page) {
            this.currentPage = page;
            this.getPermissions();
        },

        approvePermission(permission) {
            this.loadingPermissionId = permission.id;
            axios.post(`/permissions/${permission.id}/change-status`, {
                status: 'approved'
            })
            .then(response => {
                this.getPermissions();
                this.$dispatch('refresh-counters');
            })
            .catch(error => {
                console.log(error);
            })
            .finally(() => {
                this.loadingPermissionId = null;
            });
        },

        rejectPermission(permission) {
            this.loadingPermissionId = permission.id;
            axios.post(`/permissions/${permission.id}/change-status`, {
                status: 'rejected'
            })
            .then(response => {
                this.getPermissions();
                this.$dispatch('refresh-counters');
            })
            .catch(error => {
                console.log(error);
            })
            .finally(() => {
                this.loadingPermissionId = null;
            });
        },

        reviewPermission(permission) {
            this.loadingPermissionId = permission.id;
            axios.post(`/permissions/${permission.id}/change-status`, {
                status: 'for review'
            })
            .then(response => {
                this.getPermissions();
                this.$dispatch('refresh-counters');
            })
            .catch(error => {
                console.log(error);
            })
            .finally(() => {
                this.loadingPermissionId = null;
            });
        }
    };
}
</script>
