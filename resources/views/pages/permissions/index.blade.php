<x-layouts.app :title="__('Permissions')">
    <div x-data="permissionsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1>{{__('Permissions')}} <span x-text="meta.total"></span></h1>

            <flux:button variant="primary" x-on:click="openFormModal()">{{__('Add Permission')}}</flux:button>
        </div>

        <template x-if="permissions.length > 0">
            <div class="space-y-4 pb-5">
                <template x-for="permission in permissions" :key="permission.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg dark:border-gray-700">
                        <div class="flex-1 flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="text-sm text-gray-600 dark:text-gray-400" x-text="permission.date"></div>
                                <div x-show="permission.notes" class="text-sm text-gray-500 dark:text-gray-400" x-text="permission.notes"></div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span x-show="permission.type" class="px-2 py-1 text-xs font-medium rounded-full" 
                                    x-bind:class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': permission.type === 'in',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': permission.type === 'out',
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
                            <a target="_blank" :href="`/permissions/${permission.id}/pdf`" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200" title="Print">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <template x-if="permission.status === 'pending'">

                            <button x-on:click="openFormModal(permission)" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            </template>
                            <template x-if="permission.status === 'pending'">
                                <button x-on:click="deletePermission(permission.id)" class="p-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </template>
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

        <flux:modal variant="flyout" name="permission-form" focusable class="w-full md:w-1/3">
            <form x-on:submit.prevent="savePermission" class="space-y-6">
                <div>
                    <flux:heading size="lg" x-text="selectedPermission ? '{{__('Edit Permission')}}' : '{{__('Add Permission')}}'"></flux:heading>

                    <flux:subheading x-text="selectedPermission ? '{{__('Edit the permission details')}}' : '{{__('Add a new permission')}}'"></flux:subheading>
                </div>

                <flux:select x-model="form.type" :label="__('Type')">
                    <option value="">{{__('Select Type')}}</option>
                    <option value="in">{{__('In')}}</option>
                    <option value="out">{{__('Out')}}</option>
                </flux:select>
                <flux:input x-model="form.date" :label="__('Date')" type="date" />
                <flux:input x-model="form.time" :label="__('Time')" type="time" />
                <flux:input x-model="form.duration" :label="__('Duration')" type="number" />
                <flux:input x-model="form.reason" :label="__('Reason')" type="text" />
                <flux:input x-model="form.notes" :label="__('Notes')" type="text" />

                <div class="flex justify-end space-x-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </div>

    <script>
        function permissionsComponent() {
            return {
                permissions: [],
                meta: {},
                links: [],
                currentPage: 1,
                selectedPermission: null,
                form: null,

                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.form = this.getEmptyForm();
                    this.getPermissions();
                    
                },

                getPermissions() {
                    axios.get('/permissions', {
                        params: {
                            page: this.currentPage
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

                deletePermission(id) {
                    axios.delete(`/permissions/${id}`)
                    .then(response => {
                        this.currentPage = this.permissions.length == 1 ? this.currentPage - 1 : this.currentPage;
                        this.getPermissions();
                    }).catch(error => {
                        alert(error.response.data.message);
                    });
                },
                
                openFormModal(permission = null) {
                    this.selectedPermission = permission;
                    this.form = permission ? {...permission} : this.getEmptyForm();
                    Flux.modal('permission-form').show();
                },
                
                savePermission() {
                    this.submitForm();
                },
                
                submitForm() {
                    if (this.selectedPermission) {
                        url = `/permissions/${this.selectedPermission.id}`;
                        method = 'put';
                    } else {
                        url = '/permissions';
                        method = 'post';
                    }
                    axios[method](url, this.form)
                    .then(response => {
                        this.getPermissions();
                        Flux.modal('permission-form').close();
                    })
                    .catch(error => {
                        alert(error.response.data.message);
                    });
                },
                
                goToPage(page) {
                    this.currentPage = page;
                    this.getPermissions();
                },
                
                getEmptyForm() {
                    return {
                        type: '',
                        date: '',
                        time: '',
                        duration: '',
                        reason: '',
                        notes: ''
                    };
                }
            };
        }
    </script>
</x-layouts.app>
