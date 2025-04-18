<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
new class extends Component {

    use WithFileUploads;

    public $user;
    public $currentStamp;
    public $newStamp;
    public function mount()
    {
        
        $this->user = auth()->user();
        $this->currentStamp = $this->user->stamp;
    }

    public function updateStamp()
    {
        if($this->newStamp){
            // first remove all files in the stamps folder which start with the "user.id_"
            $files = Storage::disk('stamps')->files();
            foreach($files as $file){
                if(str_starts_with($file, $this->user->id . '_')){
                    unlink(Storage::disk('stamps')->path($file));
                }
            }
            
            $stampFileName = $this->user->id . '_' . now()->timestamp . '.png';

            Storage::disk('stamps')->put($stampFileName, file_get_contents($this->newStamp->getRealPath()));
            
            $this->user->stamp = $stampFileName;
            $this->user->save();
            $this->js('window.location.reload()');
        }
    }

    public function cancelUpload()
    {
        dd('dascsdacvsdv');
        $this->newStamp = null;
    }
}; ?>

<div class="flex flex-col">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Stamp')" :subheading=" __('Stamp should be added to start using the app')">

        <form wire:submit="updateStamp" class="space-y-6">

            @if($currentStamp)
                <div class="mb-10">
                    <p class="text-sm text-gray-500 dark:text-gray-300 mb-2">{{__('Current stamp')}}:</p>
                    <img  width="400" src="{{$currentStamp}}" alt="User stamp" class="border border-gray-500 dark:border-gray-600 rounded dark:bg-gray-800">
                </div>
            @endif
            <!-- Show canvas for new users or when changing stamp -->
            <flux:subheading>{{ $currentStamp ? __('Change Stamp') : __('Add Stamp') }}</flux:subheading>
            <!-- file input to upload stamp -->
             @if(!$newStamp)
                <flux:input type="file" accept="image/png" id="stampFile" wire:model="newStamp" />
                <div wire:loading wire:target="newStamp">{{ __('Uploading') }}</div>
            @endif

            @if ($newStamp) 
                <img  width="400"  src="{{ $newStamp->temporaryUrl() }}"  class="border border-gray-500 dark:border-gray-600 rounded dark:bg-gray-800">
            @endif
            <!-- display file image after upload -->
            <div class="flex justify-start space-x-2 mt-2">
                @if ($newStamp)
                    <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
                    <flux:button type="button" variant="filled" wire:click="$set('newStamp', null)">{{ __('Cancel') }}</flux:button>                
                @endif
            </div>   
        </form>
    </x-settings.layout>
</div>
