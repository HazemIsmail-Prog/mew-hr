<x-layouts.app :title="__('Users')">
    <div x-data="usersComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="flex items-center justify-between">
            <h1>{{__('Users')}} <span x-text="meta.total"></span></h1>

            <flux:button variant="primary" x-on:click="openFormModal()">{{__('Add User')}}</flux:button>
        </div>

        <template x-if="users.length > 0">
            <div class="space-y-4 pb-5">
                <template x-for="user in users" :key="user.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex-1 space-y-2">
                            <h2 
                                class="text-lg font-medium"
                                :class="{'text-red-500 dark:text-red-400': user.role == 'supervisor', 'text-gray-900 dark:text-white': user.role != 'supervisor'}"
                                x-text="user.name"
                            ></h2>
                            <div class="space-y-1">
                                <!-- <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span x-text="user.department.name"></span>
                                </p> -->
                                <!-- <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    <span x-text="user.role" class="capitalize"></span>
                                </p> -->
                                <p x-show="user.role !== 'supervisor'" class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span class="text-red-500 dark:text-red-400" x-text="user.supervisor?.name || '{{__('No Supervisor')}}'"></span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                    <span x-text="user.cid"></span>
                                </p>
                                <!-- <p class="text-sm text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span x-text="user.email"></span>
                                </p> -->
                            </div>
                        </div>
                        <div class="w-full lg:w-auto p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <img x-bind:src="user.signature" alt="User signature" class="max-h-20 border border-gray-200 dark:border-gray-600 rounded bg-white dark:bg-gray-800">
                        </div>
                        <div class="flex flex-wrap gap-2 w-full lg:w-auto">
                            <flux:button variant="filled" x-on:click="openFormModal(user)" class="w-full lg:w-auto">{{__('Edit')}}</flux:button>
                            <!-- <flux:button variant="danger" x-on:click="deleteUser(user.id)" class="w-full lg:w-auto">{{__('Delete')}}</flux:button> -->
                            <flux:button variant="filled" x-on:click="loginAsUser(user.id)" class="w-full lg:w-auto">{{__('Login as User')}}</flux:button>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        @include('components.pagination')

        <flux:modal variant="flyout" name="user-form" focusable class="w-full md:w-1/3">
            <form x-on:submit.prevent="saveUser" class="space-y-6">
                <div>
                    <flux:heading size="lg" x-text="selectedUser ? '{{__('Edit User')}}' : '{{__('Add User')}}'"></flux:heading>

                    <flux:subheading x-text="selectedUser ? '{{__('Edit the user details')}}' : '{{__('Add a new user')}}'"></flux:subheading>
                </div>

                <flux:input x-model="form.name" :label="__('Name')" type="text" />
                <flux:input x-model="form.email" :label="__('Email')" type="email" />
                <flux:select x-model="form.department_id" :label="__('Department')">
                    <option :selected="form.department_id == ''" value="">{{__('Select Department')}}</option>
                    <template x-for="department in departments" :key="department.id">
                        <option :selected="form.department_id == department.id" :value="department.id" x-text="department.name"></option>
                    </template>
                </flux:select>
                <flux:select x-model="form.role" :label="__('Role')">
                    <option :selected="form.role == ''" value="">{{__('Select Role')}}</option>
                    <option :selected="form.role == 'admin'" value="admin">{{__('Admin')}}</option>
                    <option :selected="form.role == 'supervisor'" value="supervisor">{{__('Supervisor')}}</option>
                    <option :selected="form.role == 'employee'" value="employee">{{__('Employee')}}</option>
                </flux:select>
                <flux:select x-model="form.supervisor_id" :label="__('Supervisor')">
                    <option :selected="form.supervisor_id == ''" value="">{{__('Select Supervisor')}}</option>
                    <template x-for="supervisor in supervisors" :key="supervisor.id">
                        <option :selected="form.supervisor_id == supervisor.id" :value="supervisor.id" x-text="supervisor.name"></option>
                    </template>
                </flux:select>

                <flux:input x-model="form.cid" :label="__('Civil ID')" type="number" min="100000000000" max="999999999999" required />
                <flux:input x-model="form.file_number" :label="__('File Number')" type="number" required />
                <flux:input x-model="form.password" :label="__('Password')" type="password" />


                
                <!-- Signature box moved inside the form -->
                <div class="mt-4">
                    <flux:heading size="sm">{{__('Signature')}}</flux:heading>
                    
                    <!-- Display existing signature if available -->
                        <div x-show="selectedUser && selectedUser.signature" class="mb-2">
                            <p class="text-sm text-gray-500">{{__('Current signature')}}:</p>
                            <img x-bind:src="selectedUser?.signature" alt="User signature" class="max-h-20 border border-gray-200 rounded">
                            <div class="mt-2">
                                <flux:button variant="filled" x-on:click="showSignatureCanvas = true; initializeSignatureCanvas()">{{ __('Change Signature') }}</flux:button>
                            </div>
                        </div>
                    
                    <!-- Show canvas for new users or when changing signature -->
                        <div x-show="!selectedUser || !selectedUser.signature || showSignatureCanvas">
                            <flux:subheading size="sm">{{__('Draw your signature below')}}</flux:subheading>
                            <div class="w-full overflow-x-auto">
                                <canvas id="signatureCanvas" width="400" height="200" style="border: 1px solid #ccc; max-width: 100%;"></canvas>
                            </div>
                            <div class="flex justify-end space-x-2 mt-2">
                                <flux:button variant="filled" x-on:click="clearSignature">{{ __('Clear') }}</flux:button>
                                <template x-if="selectedUser && selectedUser.signature">
                                    <flux:button variant="filled" x-on:click="showSignatureCanvas = false">{{ __('Cancel') }}</flux:button>
                                </template>
                            </div>
                        </div>
                </div>

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
        function usersComponent() {
            return {
                users: [],
                departments: @json($departments),
                supervisors: @json($supervisors),
                meta: {},
                links: [],
                currentPage: 1,
                selectedUser: null,
                form: null,
                showSignatureCanvas: false,

                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.form = this.getEmptyForm();
                    this.getUsers();
                    
                    // Initialize signature canvas when component is loaded
                    this.$nextTick(() => {
                        this.initializeSignatureCanvas();
                    });
                },

                initializeSignatureCanvas() {
                    const canvas = document.getElementById('signatureCanvas');
                    
                    if (!canvas) {
                        return;
                    }

                    // Get the context
                    const ctx = canvas.getContext('2d');
                    
                    ctx.strokeStyle = '#0066cc'; // Blue ink color
                    ctx.lineWidth = 2;
                    
                    // Clear any previous drawings
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    // Simple drawing variables
                    let isDrawing = false;
                    let lastX = 0;
                    let lastY = 0;

                    // Function to get coordinates relative to canvas
                    function getCoordinates(e) {
                        const rect = canvas.getBoundingClientRect();
                        let x, y;
                        
                        if (e.type.includes('touch')) {
                            // Touch event
                            const touch = e.touches[0] || e.changedTouches[0];
                            x = touch.clientX - rect.left;
                            y = touch.clientY - rect.top;
                        } else {
                            // Mouse event
                            x = e.clientX - rect.left;
                            y = e.clientY - rect.top;
                        }
                        
                        return { x, y };
                    }

                    // Mouse down event
                    canvas.addEventListener('mousedown', (e) => {
                        isDrawing = true;
                        const coords = getCoordinates(e);
                        lastX = coords.x;
                        lastY = coords.y;
                    });

                    // Mouse move event
                    canvas.addEventListener('mousemove', (e) => {
                        if (!isDrawing) return;
                        
                        const coords = getCoordinates(e);
                        
                        ctx.beginPath();
                        ctx.moveTo(lastX, lastY);
                        ctx.lineTo(coords.x, coords.y);
                        ctx.stroke();
                        
                        lastX = coords.x;
                        lastY = coords.y;
                    });

                    // Mouse up event
                    canvas.addEventListener('mouseup', () => {
                        isDrawing = false;
                    });

                    // Mouse out event
                    canvas.addEventListener('mouseout', () => {
                        isDrawing = false;
                    });
                    
                    // Touch events
                    canvas.addEventListener('touchstart', (e) => {
                        e.preventDefault(); // Prevent scrolling
                        isDrawing = true;
                        const coords = getCoordinates(e);
                        lastX = coords.x;
                        lastY = coords.y;
                    }, { passive: false });
                    
                    canvas.addEventListener('touchmove', (e) => {
                        e.preventDefault(); // Prevent scrolling
                        if (!isDrawing) return;
                        
                        const coords = getCoordinates(e);
                        
                        ctx.beginPath();
                        ctx.moveTo(lastX, lastY);
                        ctx.lineTo(coords.x, coords.y);
                        ctx.stroke();
                        
                        lastX = coords.x;
                        lastY = coords.y;
                    }, { passive: false });
                    
                    canvas.addEventListener('touchend', (e) => {
                        e.preventDefault(); // Prevent scrolling
                        isDrawing = false;
                    }, { passive: false });
                    
                    canvas.addEventListener('touchcancel', (e) => {
                        e.preventDefault(); // Prevent scrolling
                        isDrawing = false;
                    }, { passive: false });
                },

                getEmptyForm() {
                    return {
                        name: '',
                        email: '',
                        signature: '',
                        department_id: '',
                        cid: '',
                        role: '',
                        supervisor_id: '',
                        password: '',
                        file_number: '',
                    };
                },

                getUsers() {
                    axios.get('/users', {
                        params: {
                            page: this.currentPage
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        this.users = data.data;
                        this.meta = data.meta;
                        this.currentPage = data.meta.current_page;
                        this.links = data.meta.links;
                        this.links.shift();
                        this.links.pop();
                    });
                },

                
                deleteUser(id) {
                    axios.delete(`/users/${id}`).then(response => {
                        this.currentPage = this.users.length == 1 ? this.currentPage - 1 : this.currentPage;
                        this.getUsers();
                    });
                },
                
                openFormModal(user = null) {
                    this.selectedUser = user;
                    this.form = user ? {...user} : this.getEmptyForm();
                    this.showSignatureCanvas = !user || !user.signature; // Show canvas for new users
                    Flux.modal('user-form').show();
                    
                    // Initialize the signature canvas after a short delay
                    setTimeout(() => {
                        this.initializeSignatureCanvas();
                    }, 300);
                },

                saveUser() {
                    // Save the signature data to the form
                    const canvas = document.getElementById('signatureCanvas');
                    // check if canvas contains a signature
                    if (canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data.some(value => value !== 0)) {
                        // Convert canvas to base64 image data
                        const signatureData = canvas.toDataURL('image/png');
                        this.form.signature = signatureData;
                    }
                    else{
                        this.form.signature = null;
                    }
                    
                    // Submit the form with the signature data
                    this.submitForm();
                },
                
                submitForm() {

                    if (this.selectedUser) {
                        url = `/users/${this.selectedUser.id}`;
                        method = 'put';
                    } else {
                        url = '/users';
                        method = 'post';
                    }
                    axios[method](url, this.form)
                    .then(response => {
                        this.getUsers();
                        Flux.modal('user-form').close();
                    })
                    .catch(error => {
                        console.log(error);
                    });
                },

                goToPage(page) {
                    this.currentPage = page;
                    this.getUsers();
                },

                clearSignature() {
                    const canvas = document.getElementById('signatureCanvas');
                    
                    if (!canvas) {
                        return;
                    }
                    
                    // Get the context
                    const ctx = canvas.getContext('2d');
                    
                    // Clear the canvas
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    // Reset the form signature
                    this.form.signature = '';
                },

                loginAsUser(id) {
                    window.location.href = `/login-as-user/${id}`;
                }
            };
        }
    </script>
</x-layouts.app>
