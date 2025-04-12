    <div x-data="missionsComponent()" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

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

        <template x-if="missions.length > 0">
            <div class="space-y-4">
                <template x-for="mission in missions" :key="mission.id">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4 p-4 border rounded-lg dark:border-gray-700">
                        <div class="flex-1 flex flex-col lg:flex-row lg:items-center gap-4">
                            <div class="flex-1">
                                <h1 x-text="mission.user.name"></h1>
                                <div class="text-sm font-medium dark:text-gray-200" x-text="mission.start_date + ' - ' + mission.end_date"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400" x-text="mission.reason"></div>
                                <div x-show="mission.notes" class="text-sm text-gray-500 dark:text-gray-400" x-text="mission.notes"></div>
                                <span x-show="mission.direction" class="px-2 py-1 text-xs font-medium rounded-full" 
                                    x-bind:class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': mission.direction === 'in',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': mission.direction === 'out',
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': mission.direction === 'in-out'
                                    }"
                                    x-text="mission.translated_direction">
                                </span>
                                <span x-show="mission.status" class="px-2 py-1 text-xs font-medium rounded-full"
                                    x-bind:class="{
                                        'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200': mission.status === 'pending',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': mission.status === 'approved',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': mission.status === 'rejected',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': mission.status === 'for review'
                                    }"
                                    x-text="mission.translated_status">
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2 w-full lg:w-auto">
                            <flux:button x-show="mission.status !== 'approved'" variant="primary" x-on:click="approveMission(mission)" title="{{__('Approve')}}">
                               {{__('Approve')}}
                            </flux:button>
                            <flux:button x-show="mission.status !== 'rejected'" variant="danger" x-on:click="rejectMission(mission)" title="{{__('Reject')}}">
                                {{__('Reject')}}
                            </flux:button>
                            <flux:button x-show="mission.status !== 'for review'" variant="filled" x-on:click="reviewMission(mission)" title="{{__('Review')}}">
                                {{__('Review')}}
                            </flux:button>
                        </div>
                    </div>
                </template>
            </div>
        </template>
        <!-- non flux Pagination with top shadow -->
        <div x-cloak x-show="meta.last_page > 1" class="flex items-center justify-end gap-2 fixed bottom-0 end-0 start-0 p-4 shadow-md dark:shadow-gray-900">
            <flux:button class="select-none" x-bind:disabled="currentPage == 1" x-on:click="goToPage(currentPage - 1)">{{__('Previous')}}</flux:button>
            <template x-for="link in links" :key="link.label">
                <flux:button class="select-none !hidden lg:!block" x-bind:disabled="currentPage == link.label" x-on:click="goToPage(link.label)" x-html="link.label"></flux:button>
            </template>
            <flux:button class="select-none" x-bind:disabled="currentPage == meta.last_page" x-on:click="goToPage(currentPage + 1)">{{__('Next')}}</flux:button>
        </div>
    </div>

    <script>
        function missionsComponent() {
            return {
                missions: [],
                meta: {},
                links: [],
                currentPage: 1,
                selectedMission: null,
                filters: {
                    user: '',
                    status: 'pending'
                },


                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.getMissions();
                    this.$watch('filters', () => {
                        this.getMissions();
                    });
                },

                getMissions() {
                    axios.get('/requests/missions', {
                        params: {
                            page: this.currentPage,
                            user: this.filters.user,
                            status: this.filters.status
                        }
                    })
                    .then(response => {
                        const data = response.data;
                        this.missions = data.data;
                        this.meta = data.meta;
                        this.currentPage = data.meta.current_page;
                        this.links = data.meta.links;
                        this.links.shift();
                        this.links.pop();
                    });
                },
                
                goToPage(page) {
                    this.currentPage = page;
                    this.getMissions();
                },

                approveMission(mission) {
                        axios.post(`/missions/${mission.id}/change-status`, {
                            status: 'approved'
                        })
                        .then(response => {
                            this.getMissions();
                            this.$dispatch('refresh-counters');
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },

                rejectMission(mission) {
                        axios.post(`/missions/${mission.id}/change-status`, {
                        status: 'rejected'
                    })
                    .then(response => {
                        this.getMissions();
                        this.$dispatch('refresh-counters');
                    })
                    .catch(error => {
                        console.log(error);
                    });

                },

                reviewMission(mission) {
                    axios.post(`/missions/${mission.id}/change-status`, {
                        status: 'for review'
                    })
                    .then(response => {
                        this.getMissions();
                        this.$dispatch('refresh-counters');
                    })
                    .catch(error => {
                        console.log(error);
                    });
                }
            };
        }
    </script>
