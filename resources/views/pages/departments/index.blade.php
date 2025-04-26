<x-layouts.app :title="__('Departments')">
    <div x-data="departmentsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1>{{__('Departments')}} <span x-text="meta.total"></span></h1>

            <flux:button variant="primary" x-on:click="openFormModal()">{{__('Add Department')}}</flux:button>
        </div>

        <template x-if="departments.length > 0">
            <div class="space-y-4">
                <template x-for="department in departments" :key="department.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg">
                        <div class="flex-1">
                            <h2 class="text-lg font-medium" x-text="department.name"></h2>
                        </div>
                        <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                            <flux:button x-on:click="deleteDepartment(department.id)" class="w-full lg:w-auto">{{__('Delete')}}</flux:button>
                            <flux:button x-on:click="openFormModal(department)" class="w-full lg:w-auto">{{__('Edit')}}</flux:button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        @include('components.pagination')

        <flux:modal variant="flyout" name="department-form" focusable class="w-full md:w-1/3">
            <form x-on:submit.prevent="saveDepartment" class="space-y-6">
                <div>
                    <flux:heading size="lg" x-text="selectedDepartment ? '{{__('Edit Department')}}' : '{{__('Add Department')}}'"></flux:heading>

                    <flux:subheading x-text="selectedDepartment ? '{{__('Edit the department details')}}' : '{{__('Add a new department')}}'"></flux:subheading>
                </div>

                    <flux:input x-model="form.name" :label="__('Name')" type="text" />

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
        function departmentsComponent() {
            return {
                departments: [],
                meta: {},
                links: [],
                currentPage: 1,
                selectedDepartment: null,
                form: null,

                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.form = this.getEmptyForm();
                    this.getDepartments();
                    
                },

                getDepartments() {
                    axios.get('/departments', {
                        params: {
                            page: this.currentPage
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        this.departments = data.data;
                        this.meta = data.meta;
                        this.currentPage = data.meta.current_page;
                        this.links = data.meta.links;
                        this.links.shift();
                        this.links.pop();
                    });
                },

                deleteDepartment(id) {
                    axios.delete(`/departments/${id}`).then(response => {
                        this.currentPage = this.departments.length == 1 ? this.currentPage - 1 : this.currentPage;
                        this.getDepartments();
                    });
                },
                
                openFormModal(department = null) {
                    this.selectedDepartment = department;
                    this.form = department ? {...department} : this.getEmptyForm();
                    Flux.modal('department-form').show();
                },
                
                saveDepartment() {
                    this.submitForm();
                },
                
                submitForm() {
                    if (this.selectedDepartment) {
                        url = `/departments/${this.selectedDepartment.id}`;
                        method = 'put';
                    } else {
                        url = '/departments';
                        method = 'post';
                    }
                    axios[method](url, this.form)
                    .then(response => {
                        this.getDepartments();
                        Flux.modal('department-form').close();
                    })
                    .catch(error => {
                        console.log(error);
                    });
                },
                
                goToPage(page) {
                    this.currentPage = page;
                    this.getDepartments();
                },
                
                getEmptyForm() {
                    return {
                        name: '',
                        signature: ''
                    };
                }
            };
        }
    </script>
</x-layouts.app>
