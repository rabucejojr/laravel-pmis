<x-app-layout title="Edit User">
    <div class="max-w-lg">
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('users.index') }}" class="hover:underline" style="color:#003087;">User Management</a>
                / Edit
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">Edit User</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-form-field label="Full Name" name="name" required>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $user->name) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                </x-form-field>

                <x-form-field label="Email Address" name="email" required>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Role" name="role" required>
                        <select name="role" id="role"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                            @foreach(['admin','encoder','verifier','viewer'] as $r)
                                <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>
                                    {{ ucfirst($r) }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Office / Division" name="office">
                        <input type="text" name="office" id="office"
                               value="{{ old('office', $user->office) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('office') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <div class="rounded-lg p-4 border border-gray-100" style="background-color:#F5F7FA;">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                        Change Password <span class="font-normal normal-case text-gray-400">(leave blank to keep current)</span>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form-field label="New Password" name="password">
                            <input type="password" name="password" id="password"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                        </x-form-field>

                        <x-form-field label="Confirm New Password" name="password_confirmation">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   placeholder="Repeat new password"
                                   autocomplete="new-password"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </x-form-field>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                          onsubmit="return confirm('Delete user {{ addslashes($user->name) }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50">
                            Delete User
                        </button>
                    </form>
                    @else
                    <div></div>
                    @endif

                    <div class="flex items-center gap-3">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background-color:#003087;"
                                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
