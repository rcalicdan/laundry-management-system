<section class="w-full">
    <x-contents.heading title="Update Laundry Service" />

    <x-contents.layout>
        <x-flash-session />
        <div class="p-4 sm:p-6 lg:p-8">
            <form wire:submit.prevent='update' class="space-y-6">
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
                    <input type="number" step="0.01" id="price_per_kg" name="price_per_kg" wire:model.live='price_per_kg'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter price per kilogram" required>

                    @error('price_per_kg')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update
                    </button>
                    <a href="{{ route('laundry-services.table') }}" wire:navigate
                        class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </x-contents.layout>
</section>