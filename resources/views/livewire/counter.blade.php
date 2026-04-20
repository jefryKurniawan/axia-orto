<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <h2 class="text-xl font-semibold mb-2">Counter</h2>
    <div class="text-4xl font-bold text-blue-600 mb-4">{{ $count }}</div>
    <button 
        wire:click="increment" 
        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
    >
        Increment
    </button>
</div>
