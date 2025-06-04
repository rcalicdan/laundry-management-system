@props(['wireClick' => 'delete'])
<button wire:click='{{ $wireClick }}' wire:confirm='Are you sure you want to delete this item?'
    class="text-red-600 hover:text-red-900 font-semibold">Delete</button>