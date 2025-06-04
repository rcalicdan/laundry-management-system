<section class="w-full">
    <x-contents.heading title="Laundry Service Management" />

    <x-contents.layout>
        <div x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false"
            class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Laundry Services" />
                <x-utils.create-button createButtonName="Add New Service" :route="route('laundry-services.create')" />
            </x-contents.table-head>

            <x-flash-session />

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price per KG</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estimited Completion</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($laundryServices as $service)
                        <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $service->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $service->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                ₱{{ number_format($service->price_per_kg, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $service->estimated_time ?? 'Unknown'}} minutes
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $service->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    @can('update', $service)
                                    <x-utils.update-button :route="route('laundry-services.edit', [$service->id])" />
                                    @endcan

                                    @can('delete', $service)
                                    <x-utils.delete-button wireClick="delete({{ $service->id }})" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $laundryServices->links() }}
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Laundry Services" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-id" class="block text-sm font-medium text-gray-700">ID</label>
                    <input type="text" id="search-id" wire:model='searchId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter service ID">
                </div>

                <div>
                    <label for="search-name" class="block text-sm font-medium text-gray-700">Service Name</label>
                    <input type="text" id="search-name" wire:model='searchName'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter service name">
                </div>

                <div>
                    <label for="search-price" class="block text-sm font-medium text-gray-700">Price per KG</label>
                    <input type="number" step="0.01" id="search-price" wire:model='searchPrice'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter price">
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>