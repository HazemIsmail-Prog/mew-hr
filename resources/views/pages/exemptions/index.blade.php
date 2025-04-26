<x-layouts.app :title="__('Exemptions')">
    <div x-data="exemptionsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
        <h1>{{__('Exemptions')}} <span x-text="meta.total"></span></h1>

            <flux:button variant="primary" x-on:click="openFormModal()">{{__('Add Exemption')}}</flux:button>
        </div>

        <template x-if="exemptions.length > 0">
            <div class="space-y-4 pb-5">
                <template x-for="exemption in exemptions" :key="exemption.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg dark:border-gray-700">
                        <div class="flex-1 flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1">
                                <div class="text-sm font-medium dark:text-gray-200" x-text="exemption.date"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400" x-text="exemption.reason"></div>
                                <div x-show="exemption.notes" class="text-sm text-gray-500 dark:text-gray-400" x-text="exemption.notes"></div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span x-show="exemption.direction" class="px-2 py-1 text-xs font-medium rounded-full" 
                                    x-bind:class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': exemption.direction === 'in',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': exemption.direction === 'out',
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': exemption.direction === 'in-out'
                                    }"
                                    x-text="exemption.translated_direction">
                                </span>
                                <span x-show="exemption.status" class="px-2 py-1 text-xs font-medium rounded-full"
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
                            <a target="_blank" :href="`/exemptions/${exemption.id}`" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200" title="Print">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <template x-if="exemption.status === 'pending'">

                            <button x-on:click="openFormModal(exemption)" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            </template>
                            <template x-if="exemption.status === 'pending'">
                                <button x-on:click="deleteExemption(exemption.id)" class="p-2 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200" title="Delete">
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

        @include('components.pagination')

        <flux:modal variant="flyout" name="exemption-form" focusable class="w-full md:w-1/3">
            <form x-on:submit.prevent="saveExemption" class="space-y-6">
                <div>
                    <flux:heading size="lg" x-text="selectedExemption ? '{{__('Edit Exemption')}}' : '{{__('Add Exemption')}}'"></flux:heading>

                    <flux:subheading x-text="selectedExemption ? '{{__('Edit the exemption details')}}' : '{{__('Add a new exemption')}}'"></flux:subheading>
                </div>

                <flux:select x-model="form.direction" :label="__('Direction')">
                    <option :selected="form.direction == ''" value="">{{__('Select Direction')}}</option>
                    <option :selected="form.direction == 'in'" value="in">{{__('In')}}</option>
                    <option :selected="form.direction == 'out'" value="out">{{__('Out')}}</option>
                    <option :selected="form.direction == 'in-out'" value="in-out">{{__('In & Out')}}</option>
                </flux:select>
                <flux:input x-model="form.date" :label="__('Date')" type="date" />
                <flux:input x-model="form.reason" :label="__('Reason')" type="text" />
                <flux:input x-model="form.notes" :label="__('Notes')" type="text" />
                <p class="text-sm text-red-500 dark:text-red-400 mt-2">{{__('in case of multipule days exemption paper should be provided')}}</p>
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
        function exemptionsComponent() {
            return {
                exemptions: [],
                meta: {},
                links: [],
                currentPage: 1,
                selectedExemption: null,
                form: null,

                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.form = this.getEmptyForm();
                    this.getExemptions();
                    
                },

                getExemptions() {
                    axios.get('/exemptions', {
                        params: {
                            page: this.currentPage
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
                    });
                },

                deleteExemption(id) {
                    axios.delete(`/exemptions/${id}`)
                        .then(response => {
                            this.currentPage = this.exemptions.length == 1 ? this.currentPage - 1 : this.currentPage;
                            this.getExemptions();
                        }).catch(error => {
                            alert(error.response.data.message);
                        });
                },
                
                openFormModal(exemption = null) {
                    this.selectedExemption = exemption;
                    this.form = exemption ? {...exemption} : this.getEmptyForm();
                    Flux.modal('exemption-form').show();
                },
                
                saveExemption() {
                    this.submitForm();
                },
                
                submitForm() {
                    if (this.selectedExemption) {
                        url = `/exemptions/${this.selectedExemption.id}`;
                        method = 'put';
                    } else {
                        url = '/exemptions';
                        method = 'post';
                    }
                    axios[method](url, this.form)
                    .then(response => {
                        this.getExemptions();
                        Flux.modal('exemption-form').close();
                    })
                    .catch(error => {
                        alert(error.response.data.message);
                    });
                },
                
                goToPage(page) {
                    this.currentPage = page;
                    this.getExemptions();
                },
                
                getEmptyForm() {
                    return {
                        direction: '',
                        date: new Date().toISOString().split('T')[0],
                        reason: '',
                        notes: ''
                    };
                }
            };
        }
    </script>
</x-layouts.app>
