<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new class extends Component {

    public $user;
    public $currentSignature;

    public function mount()
    {
        
        $this->user = auth()->user();
        $this->currentSignature = $this->user->signature;
    }

    public function updateSignature($newSignature)
    {
        if($newSignature){
            $signaturePath = $this->saveSignatureAndReturnItsFullPath($newSignature, $this->user);
            if($signaturePath){
                $this->user->signature = $signaturePath;
                $this->user->save();
                $this->js('window.location.reload()');
            }
        }
    }

    private function saveSignatureAndReturnItsFullPath($signature, $user)
    {

        // first remove all files in the signatures folder which start with the "user.id_"
        $files = Storage::disk('signatures')->files();
        foreach($files as $file){
            if(str_starts_with($file, $user->id . '_')){
                unlink(Storage::disk('signatures')->path($file));
            }
        }

        // Handle signature if provided
        if (isset($signature) && !empty($signature)) {
            
            // Save signature to file
            $signatureData = $signature;
            $signatureFileName = $user->id . '_' . now()->timestamp . '.png';
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
            
            // Decode the base64 data
            $imageData = base64_decode($base64Data);
            // dd($imageData);

            Storage::disk('signatures')->put($signatureFileName, $imageData);
            
            // Update the signature path in the data
            return $signatureFileName;
        }
        return null;
    }
}; ?>

<div class="flex flex-col" x-data="signatureComponent">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Signature')" :subheading=" __('Signature should be added to start using the app')">

        <form x-on:submit.prevent="submitSignature" class="space-y-6">

            <!-- Display existing signature if available -->
            <div x-show="currentSignature" class="mb-2">
                <p class="text-sm text-gray-500 dark:text-gray-300 mb-2">{{__('Current signature')}}:</p>
                <img  width="400" height="200" x-bind:src="currentSignature" alt="User signature" class="border border-gray-200 dark:border-gray-600 rounded dark:bg-gray-800">
                <div class="mt-2">
                    <flux:button variant="filled" x-on:click="showSignatureCanvas = true; initializeSignatureCanvas()">{{ __('Change Signature') }}</flux:button>
                </div>
            </div>
        
            <!-- Show canvas for new users or when changing signature -->
            <div x-cloak x-show="!currentSignature || showSignatureCanvas">
                <flux:subheading size="sm" class="dark:text-gray-300 mb-2">{{__('Draw your signature below')}}</flux:subheading>
                <div class="w-full overflow-x-auto">
                    <canvas id="signatureCanvas" width="400" height="200" class="border border-dashed border-gray-200 dark:border-gray-600 rounded dark:bg-gray-800" style="max-width: 100%;"></canvas>
                </div>   
                <div class="flex justify-start space-x-2 mt-2">
                    <flux:button x-cloak x-show="hasDrawing" variant="primary" type="submit">{{ __('Save') }}</flux:button>
                    <flux:button x-cloak x-show="hasDrawing" variant="filled" x-on:click="clearSignature">{{ __('Clear') }}</flux:button>
                    <flux:button x-cloak x-show="currentSignature" variant="filled" x-on:click="closeSignatureCanvas">{{ __('Cancel') }}</flux:button>                </div>   
            </div>

        </form>
    </x-settings.layout>
</div>

<script>
    function signatureComponent() {
            return {
                selectedUser: @js($user),
                currentSignature: @js($currentSignature),
                newSignature: '',
                showSignatureCanvas: false,
                hasDrawing: false,

                init() {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
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
                        
                        // Set hasDrawing to true when drawing occurs
                        this.hasDrawing = true;
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
                        
                        // Set hasDrawing to true when drawing occurs
                        this.hasDrawing = true;
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

                submitSignature() {

                    // Save the signature data to the form
                    const canvas = document.getElementById('signatureCanvas');
                    // check if canvas contains a signature
                    if (this.hasDrawing) {
                        // Convert canvas to base64 image data
                        const signatureData = canvas.toDataURL('image/png');
                        this.newSignature = signatureData;
                        @this.updateSignature(this.newSignature);
                    }
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
                    this.newSignature = '';
                    
                    // Reset hasDrawing flag
                    this.hasDrawing = false;
                },

                closeSignatureCanvas() {
                    this.showSignatureCanvas = false;
                    this.hasDrawing = false;
                    this.newSignature = '';
                }
            };
    }
</script>
