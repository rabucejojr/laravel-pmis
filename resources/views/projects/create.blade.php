<x-app-layout title="New Project">
    <div class="max-w-full">
        <div class="mb-6">
            <h2 class="text-2xl font-bold" style="color:#003087;">New Project</h2>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / Create
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Program" name="program_id" required class="sm:col-span-1">
                        <select name="program_id" id="program_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-400 @enderror">
                            <option value="">Select program…</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}"
                                    {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->code }} — {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Status" name="status" required class="sm:col-span-1">
                        <select name="status" id="status"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-400 @enderror">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended
                            </option>
                            <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated
                            </option>
                        </select>
                    </x-form-field>
                </div>

                <x-form-field label="Project Title" name="title" required>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                        placeholder="Full project title">
                </x-form-field>

                <x-form-field label="Implementing Agency" name="implementing_agency" required>
                    <input type="text" name="implementing_agency" id="implementing_agency"
                        value="{{ old('implementing_agency') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('implementing_agency') border-red-400 @enderror"
                        placeholder="Agency or institution name">
                </x-form-field>

                <x-form-field label="Co-Implementing Agency" name="co_implementing_agency">
                    <input type="text" name="co_implementing_agency" id="co_implementing_agency"
                        value="{{ old('co_implementing_agency') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Co-Implementing Agency, if any">
                </x-form-field>

                <x-form-field label="Location" name="location">
                    <input type="text" name="location" id="location" value="{{ old('location') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Municipality, Province">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Start Date" name="start_date" required>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="End Date" name="end_date" required>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <x-form-field label="Total Approved Budget (₱)" name="total_approved_budget" required>
                    <input type="number" name="total_approved_budget" id="total_approved_budget"
                        value="{{ old('total_approved_budget') }}" min="0" step="0.01"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_approved_budget') border-red-400 @enderror"
                        placeholder="0.00">
                </x-form-field>

                <x-form-field label="Description" name="description">
                    <textarea name="description" id="description" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Brief description of the project…">{{ old('description') }}</textarea>
                </x-form-field>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('projects.index') }}"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition"
                        style="background-color:#003087;" onmouseover="this.style.opacity='.85'"
                        onmouseout="this.style.opacity='1'">
                        Create Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
