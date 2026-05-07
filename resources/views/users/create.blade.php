<x-app-layout title="Add User">
    <div class="max-w-lg">
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('users.index') }}" class="hover:underline" style="color:#003087;">User Management</a>
                / Add
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">Add User</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <x-form-field label="Full Name" name="name" required>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="e.g. Juan dela Cruz"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                </x-form-field>

                <x-form-field label="Email Address" name="email" required>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           placeholder="user@dost.gov.ph"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Role" name="role" required>
                        <select name="role" id="role"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-400 @enderror">
                            @foreach(['admin','encoder','verifier','viewer'] as $r)
                                <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Office / Division" name="office">
                        <input type="text" name="office" id="office"
                               value="{{ old('office') }}"
                               placeholder="e.g. PSTO-SDN"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('office') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Password" name="password" required>
                        <input type="password" name="password" id="password"
                               placeholder="Min. 8 characters"
                               autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Confirm Password" name="password_confirmation" required>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Repeat password"
                               autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </x-form-field>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background-color:#003087;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
