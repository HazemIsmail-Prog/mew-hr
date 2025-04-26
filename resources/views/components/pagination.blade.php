<div 
    x-cloak 
    x-show="meta.last_page > 1" 
    class="flex bg-zinc-50 dark:bg-zinc-900 items-center justify-end gap-2 fixed bottom-0 end-0 start-0 p-2"
>
    <flux:button
        class="select-none dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
        size="sm"
        x-bind:disabled="currentPage == 1" 
        x-on:click="goToPage(currentPage - 1)"
        icon="chevron-right"
    />

    <template x-for="link in links" :key="link.label">
        <flux:button 
            class="select-none !hidden lg:!block"
            x-bind:class="{'!bg-gray-200 !dark:bg-gray-700 dark:!text-gray-700 !dark:hover:bg-gray-600': currentPage == link.label}"
            size="sm"
            x-bind:disabled="currentPage == link.label"
            x-on:click="goToPage(link.label)"
            x-html="link.label"
        />
    </template>

    <flux:button
        class="select-none dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600" 
        size="sm" 
        x-bind:disabled="currentPage == meta.last_page" 
        x-on:click="goToPage(currentPage + 1)"
        icon="chevron-left"
    />
</div>