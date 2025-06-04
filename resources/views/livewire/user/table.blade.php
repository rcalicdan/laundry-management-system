<section class="w-full">
    <x-contents.heading title="User Management" />

    <x-contents.layout>
        <div x-data="{ isSearchModalOpen: false }" @search-completed.window="isSearchModalOpen = false"
            class="p-4 sm:p-6 lg:p-8">

            <x-contents.table-head>
                <x-utils.search-button searchButtonName="Search Users" />
                <x-utils.create-button createButtonName="Add New User" :route="route('users.create')" />
            </x-contents.table-head>

            <x-flash-session/>
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
                                Role</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hire Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                        <tr class="hover:bg-gray-100 transition-colors duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $user->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <x-utils.update-button :route="route('users.edit', [$user->id])" />
                                <x-utils.delete-button wireClick="delete({{ $user->id }})"/>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Search Modal -->
            <x-modals.search-form title="Search Users" :isSearchModalOpen="$isSearchModalOpen">
                <div>
                    <label for="search-id" class="block text-sm font-medium text-gray-700">ID</label>
                    <input type="text" id="search-id" wire:model='searchId'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter user ID">
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
                    <label for="search-hire-date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <input type="date" id="search-hire-date" wire:model='searchHireDate'
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </x-modals.search-form>

        </div>
    </x-contents.layout>
</section>