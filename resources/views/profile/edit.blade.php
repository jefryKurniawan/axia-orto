@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Information Form -->
                <div class="md:col-span-2">
                    <div class="space-y-6">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h2>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Password Update Form -->
                        <form action="{{ route('profile.update.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Update Password</h2>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                        <input type="password" name="current_password" id="current_password"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('current_password') border-red-300 @enderror">
                                        @error('current_password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                        <input type="password" name="new_password" id="new_password"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('new_password') border-red-300 @enderror">
                                        @error('new_password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Delete Account Form -->
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Delete Account</h2>
                            <p class="text-sm text-gray-500 mb-4">
                                Once your account is deleted, all of its resources and data will be permanently deleted. 
                                Before deleting your account, please download any data you wish to retain.
                            </p>
                            
                            <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirmDelete()">
                                @csrf
                                @method('DELETE')
                                
                                <div class="mb-4">
                                    <label for="delete_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="password" id="delete_password" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm @error('password') border-red-300 @enderror">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Profile Sidebar -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-center">
                        <div class="mx-auto h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-4xl font-bold text-gray-500">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Account Information</h4>
                        <div class="space-y-2 text-sm text-gray-500">
                            <div class="flex justify-between">
                                <span>Member since</span>
                                <span>{{ $user->created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Last login</span>
                                <span>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Role</span>
                                <span class="font-medium text-indigo-600">{{ ucfirst($user->role) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete your account? This action cannot be undone.');
    }
</script>
@endpush