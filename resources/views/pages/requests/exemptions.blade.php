
<x-layouts.app :title="__('Exemptions')">

    <div x-data="exemptionsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <h1>{{__('Exemptions')}} <span x-text="meta.total"></span></h1>
        </div>
    
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
            <flux:button x-cloak x-show="filters.status === 'pending' && exemptions.length > 0" x-on:click="approveCurrentPage()" variant="primary" size="sm">{{__('Approve Current Page')}}</flux:button>
            
        </div>

        <div x-show="isLoading" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
            <div class="relative">
                <div class="h-12 w-12 rounded-full border-4 border-gray-200 dark:border-gray-700"></div>
                <div class="absolute top-0 left-0 h-12 w-12 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
            </div>
        </div>

        <template x-if="exemptions.length > 0 && !isLoading">
            <div class="space-y-4 pb-5">
                <template x-for="exemption in exemptions" :key="exemption.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-6 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-1 flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1 space-y-1">
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="exemption.user.name"></h1>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <span x-text="exemption.translated_approved_exemptions_count + ' = ' + exemption.approved_exemptions_count"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="exemption.date"></div>
                                    <span x-show="exemption.direction" class="px-3 py-1 text-xs font-medium rounded-full"
                                        x-bind:class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': exemption.direction === 'in',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': exemption.direction === 'out',
                                            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': exemption.direction === 'in-out'
                                        }"
                                        x-text="exemption.translated_direction">
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium" x-text="exemption.reason"></div>
                                <div x-show="exemption.notes" class="text-sm text-gray-500 dark:text-gray-400" x-text="exemption.notes"></div>
                                <span x-show="exemption.status" class="px-3 py-1 text-xs font-medium rounded-full"
                                    x-bind:class="{
                                        'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200': exemption.status === 'pending',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': exemption.status === 'approved',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': exemption.status === 'rejected',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': exemption.status === 'for review'
                                    }"
                                    x-text="exemption.translated_status">
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 w-full lg:w-auto">                            
                            <div class="flex gap-2 w-full lg:w-auto">
                                <flux:button x-show="exemption.status !== 'approved'" variant="primary" x-on:click="changeStatus(exemption, 'approved')" title="{{__('Approve')}}" class="min-w-[100px]">
                                    {{__('Approve')}}
                                </flux:button>
                                <flux:button x-show="exemption.status !== 'rejected'" variant="danger" x-on:click="changeStatus(exemption, 'rejected')" title="{{__('Reject')}}" class="min-w-[100px]">
                                    {{__('Reject')}}
                                </flux:button>
                                <flux:button x-show="exemption.status !== 'for review'" variant="filled" x-on:click="changeStatus(exemption, 'for review')" title="{{__('Review')}}" class="min-w-[100px]">
                                    {{__('Review')}}
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        @include('components.pagination')

    </div>
    
    <script>
        function exemptionsComponent() {
            return {
                exemptions: [],
                meta: {},
                links: [],
                currentPage: 1,
                selectedExemption: null,
                isLoading: false,
                filters: {
                    user: '',
                    status: 'pending'
                },
    
    
                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.getExemptions();
                    this.$watch('filters', () => {
                        this.currentPage = 1;
                        this.getExemptions();
                    });
                },
    
                getExemptions() {
                    this.isLoading = true;
                    axios.get('/requests/exemptions', {
                        params: {
                            page: this.currentPage,
                            user: this.filters.user,
                            status: this.filters.status
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        this.exemptions = data.data;
                        this.meta = data.meta;
                        this.currentPage = data.meta.current_page;
                        this.links = data.meta.links;
                        this.links.shift();
                        this.links.pop();
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },
                
                goToPage(page) {
                    this.currentPage = page;
                    this.getExemptions();
                },
    
                changeStatus(exemption, status) {
                    this.isLoading = true;
                    axios.post(`/exemptions/${exemption.id}/change-status`, {
                        status: status
                    })
                    .then(response => {
                        document.dispatchEvent(new CustomEvent('requests-updated'));
                        this.getExemptions();
                    })
                    .catch(error => {
                        console.log(error);
                    });
                },
    
                approveCurrentPage() {
                    this.isLoading = true;
                    axios.post('/exemptions/mass-approve', {
                        exemptions: this.exemptions.map(exemption => exemption.id)
                    })
                    .then(response => {
                        document.dispatchEvent(new CustomEvent('requests-updated'));
                        this.getExemptions();
                    });
                },
            };
        }
    </script>

</x-layouts.app>



