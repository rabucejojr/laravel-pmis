<x-app-layout title="Edit Project">
    <div class="max-w-full">
        <div class="mb-6">
            <h2 class="text-2xl font-bold" style="color:#003087;">Edit Project</h2>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
                / Edit
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Program" name="program_id" required>
                        <select name="program_id" id="program_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-400 @enderror">
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}"
                                    {{ old('program_id', $project->program_id) == $program->id ? 'selected' : '' }}>
                                    {{ $program->code }} — {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Status" name="status" required>
                        <select name="status" id="status"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['active','liquidated','unliquidated'] as $s)
                                <option value="{{ $s }}" {{ old('status', $project->status) === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>

                <x-form-field label="Project Title" name="title" required>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $project->title) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                </x-form-field>

                <x-form-field label="Implementing Agency" name="implementing_agency" required>
                    <input type="text" name="implementing_agency" id="implementing_agency"
                           value="{{ old('implementing_agency', $project->implementing_agency) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('implementing_agency') border-red-400 @enderror">
                </x-form-field>

                <x-form-field label="Co-Implementing Agency" name="co_implementing_agency">
                    <input type="text" name="co_implementing_agency" id="co_implementing_agency"
                           value="{{ old('co_implementing_agency', $project->co_implementing_agency) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Co-implementing agency, if any">
                </x-form-field>

                <x-form-field label="Location" name="location">
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $project->location) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Start Date" name="start_date" required>
                        <input type="date" name="start_date" id="start_date"
                               value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="End Date" name="end_date" required>
                        <input type="date" name="end_date" id="end_date"
                               value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <x-form-field label="Total Approved Budget (₱)" name="total_approved_budget" required>
                    <input type="number" name="total_approved_budget" id="total_approved_budget"
                           value="{{ old('total_approved_budget', $project->total_approved_budget) }}"
                           min="0" step="0.01"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_approved_budget') border-red-400 @enderror">
                </x-form-field>

                <x-form-field label="Description" name="description">
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $project->description) }}</textarea>
                </x-form-field>

                <div class="flex items-center justify-between pt-2">
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Delete this project? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        @if(auth()->user()->isAdmin())
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50">
                            Delete Project
                        </button>
                        @endif
                    </form>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('projects.show', $project) }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background-color:#003087;" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
