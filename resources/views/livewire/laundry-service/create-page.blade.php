<section class="w-full">
    <x-contents.heading title="Create New Laundry Service" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='create' class="space-y-6">
                <!-- Service Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Service Name</label>
                    <input type="text" id="name" name="name" wire:model.live='name'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter service name (e.g., Wash & Fold, Dry Clean)" required>

                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price per KG Field -->
                <div>
                    <label for="price_per_kg" class="block text-sm font-medium text-gray-700">Price per KG (₱)</label>
                    <input type="number" step="0.01" id="price_per_kg" name="price_per_kg"
                        wire:model.live='price_per_kg'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter price per kilogram" required>

                    @error('price_per_kg')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_time" class="block text-sm font-medium text-gray-700">Estimated Time (In
                        Minutes)</label>
                    <input type="number" id="estimated_time" name="estimated_time" wire:model.live='estimated_time'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter price per kilogram" required>

                    @error('estimated_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <x-utils.submit-button wire-target="create" button-text="Create Service" />
                    <x-utils.link-button :href="route('laundry-services.table')" button-text="Cancel" />
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>
