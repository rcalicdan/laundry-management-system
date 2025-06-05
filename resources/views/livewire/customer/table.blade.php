<section class="w-full">
    <x-contents.heading title="Customer Management" />

    <x-contents.layout>
        <div x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false"
            class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Customers" />
                <x-utils.create-button createButtonName="Add New Customer" :route="route('customers.create')" />
            </x-contents.table-head>

            <x-flash-session />
            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Id</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Phone</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Address</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($customers as $customer)
                        <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $customer->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ Str::limit($customer->address, 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $customer->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <x-utils.view-button :route="route('customers.orders', $customer->id)" />

                                    @can('update', $customer)
                                    <x-utils.update-button :route="route('customers.edit', [$customer->id])" />
                                    @endcan

                                    @can('delete', $customer)
                                    <x-utils.delete-button wireClick="delete({{ $customer->id }})" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Customers" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-id" class="block text-sm font-medium text-gray-700">ID</label>
                    <input type="text" id="search-id" wire:model='searchId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter customer ID">
                </div>

                <div>
                    <label for="search-name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="search-name" wire:model='searchName'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter name">
                </div>

                <div>
                    <label for="search-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="search-email" wire:model='searchEmail'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter email">
                </div>

                <div>
                    <label for="search-phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" id="search-phone" wire:model='searchPhone'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter phone number">
                </div>

                <div>
                    <label for="search-created-date" class="block text-sm font-medium text-gray-700">Created
                        Date</label>
                    <input type="date" id="search-created-date" wire:model='searchCreatedDate'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>