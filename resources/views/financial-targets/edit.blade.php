<x-app-layout title="Edit Financial Target">
    <div class="max-w-full">
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">
                <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
                / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
                / <a href="{{ route('projects.financial-targets.index', $project) }}" class="hover:underline" style="color:#003087;">Financial Targets</a>
                / Edit
            </p>
            <h2 class="text-2xl font-bold" style="color:#003087;">Edit Financial Target</h2>
        </div>

        @if($financialTarget->verification_notes)
        <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-800">
            <p class="font-semibold mb-1">Verification Flags</p>
            @foreach(explode("\n", $financialTarget->verification_notes) as $note)
                <p class="text-xs">• {{ $note }}</p>
            @endforeach
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('projects.financial-targets.update', [$project, $financialTarget]) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <x-form-field label="Year" name="year" required>
                        <input type="number" name="year" id="year"
                               value="{{ old('year', $financialTarget->year) }}"
                               min="2000" max="2099"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Quarter" name="quarter" required>
                        <select name="quarter" id="quarter"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach([1,2,3,4] as $q)
                                <option value="{{ $q }}" {{ old('quarter', $financialTarget->quarter) == $q ? 'selected' : '' }}>Q{{ $q }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Month" name="month" required>
                        <select name="month" id="month"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ old('month', $financialTarget->month) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>

                <x-form-field label="Line Item" name="line_item" required>
                    <input type="text" name="line_item" id="line_item"
                           value="{{ old('line_item', $financialTarget->line_item) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('line_item') border-red-400 @enderror">
                </x-form-field>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <x-form-field label="Target Amount (₱)" name="target_amount" required>
                        <input type="number" name="target_amount" id="target_amount"
                               value="{{ old('target_amount', $financialTarget->target_amount) }}"
                               min="0" step="0.01"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('target_amount') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Obligated Amount (₱)" name="obligated_amount" required>
                        <input type="number" name="obligated_amount" id="obligated_amount"
                               value="{{ old('obligated_amount', $financialTarget->obligated_amount) }}"
                               min="0" step="0.01"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('obligated_amount') border-red-400 @enderror">
                    </x-form-field>

                    <x-form-field label="Disbursed Amount (₱)" name="disbursed_amount" required>
                        <input type="number" name="disbursed_amount" id="disbursed_amount"
                               value="{{ old('disbursed_amount', $financialTarget->disbursed_amount) }}"
                               min="0" step="0.01"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('disbursed_amount') border-red-400 @enderror">
                    </x-form-field>
                </div>

                <div class="flex items-center justify-between pt-2">
                    @if(auth()->user()->isAdmin())
                    <form method="POST"
                          action="{{ route('projects.financial-targets.destroy', [$project, $financialTarget]) }}"
                          onsubmit="return confirm('Delete this entry?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50">
                            Delete
                        </button>
                    </form>
                    @else
                    <div></div>
                    @endif

                    <div class="flex items-center gap-3">
                        <a href="{{ route('projects.financial-targets.index', $project) }}"
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
