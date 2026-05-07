<x-app-layout title="Add Physical Accomplishment">
    <div class="max-w-full">
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
                / <a href="{{ route('projects.physical-accomplishments.index', $project) }}" class="hover:underline" style="color:#003087;">Physical Accomplishments</a>
                / Add
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">Add Physical Accomplishment</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.physical-accomplishments.store', $project) }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <x-form-field label="Year" name="year" required>
                        <input type="number" name="year" id="year"
                               value="{{ old('year', date('Y')) }}"
                               min="2000" max="2099"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Quarter" name="quarter" required>
                        <select name="quarter" id="quarter"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quarter') border-red-400 @enderror">
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}" {{ old('quarter') == $q ? 'selected' : '' }}>Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Month" name="month" required>
                        <select name="month" id="month"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('month') border-red-400 @enderror">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ old('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>

                <x-form-field label="Indicator Name" name="indicator_name" required>
                    <input type="text" name="indicator_name" id="indicator_name"
                           value="{{ old('indicator_name') }}"
                           placeholder="e.g. No. of beneficiaries trained"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('indicator_name') border-red-400 @enderror">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-form-field label="Target Value" name="target_value" required>
                        <input type="number" name="target_value" id="target_value"
                               value="{{ old('target_value', '0.00') }}"
                               min="0" step="0.01"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('target_value') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Accomplished Value" name="accomplished_value" required>
                        <input type="number" name="accomplished_value" id="accomplished_value"
                               value="{{ old('accomplished_value', '0.00') }}"
                               min="0" step="0.01"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('accomplished_value') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <p class="text-xs text-gray-400">
                    Accomplishment rate is computed automatically from the values above.
                </p>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('projects.physical-accomplishments.index', $project) }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background-color:#003087;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
