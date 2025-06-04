<div class="flex items-start max-md:flex-col">
    <div class="flex-1 self-stretch max-md:pt-6 w-full">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>