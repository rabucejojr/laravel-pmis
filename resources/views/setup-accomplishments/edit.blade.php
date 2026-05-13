<x-app-layout title="Edit SETUP KPI Record">
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">
            <a href="{{ route('projects.index') }}" class="hover:underline" style="color:#003087;">Projects</a>
            / <a href="{{ route('projects.show', $project) }}" class="hover:underline" style="color:#003087;">{{ Str::limit($project->title, 40) }}</a>
            / <a href="{{ route('projects.setup-accomplishments.index', $project) }}" class="hover:underline" style="color:#003087;">SETUP KPI Accomplishments</a>
            / Edit {{ $setupAccomplishment->year }}
        </p>
        <h2 class="text-2xl font-bold" style="color:#003087;">Edit SETUP KPI Record — {{ $setupAccomplishment->year }}</h2>
    </div>

    @if($setupAccomplishment->verification_notes && in_array($setupAccomplishment->verified_status, ['flagged', 'rejected']))
    <div class="mb-4 flex items-start gap-2 rounded-lg px-4 py-3 text-sm bg-amber-50 border border-amber-200 text-amber-800">
        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p class="font-semibold mb-0.5">Verification note:</p>
            <p>{{ $setupAccomplishment->verification_notes }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST"
              action="{{ route('projects.setup-accomplishments.update', [$project, $setupAccomplishment]) }}"
              class="space-y-6">
            @csrf
            @method('PUT')

            <x-form-field label="Year" name="year" required>
                <input type="number" name="year" id="year"
                       value="{{ old('year', $setupAccomplishment->year) }}"
                       min="2000" max="2099"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-400 @enderror"
                       style="max-width:160px;">
                @error('year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </x-form-field>

            {{-- KPI grid --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">KPI Indicators</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-1/3">Indicator</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-1/3">Target</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-1/3">Actual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">

                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">Number of Projects</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="target_num_projects" min="0"
                                           value="{{ old('target_num_projects', $setupAccomplishment->target_num_projects) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('target_num_projects') border-red-400 @enderror">
                                    @error('target_num_projects')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="actual_num_projects" min="0"
                                           value="{{ old('actual_num_projects', $setupAccomplishment->actual_num_projects) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('actual_num_projects') border-red-400 @enderror">
                                    @error('actual_num_projects')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">iFUND Amount (₱)</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="target_ifund_amount" min="0" step="0.01"
                                           value="{{ old('target_ifund_amount', $setupAccomplishment->target_ifund_amount) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('target_ifund_amount') border-red-400 @enderror">
                                    @error('target_ifund_amount')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="actual_ifund_amount" min="0" step="0.01"
                                           value="{{ old('actual_ifund_amount', $setupAccomplishment->actual_ifund_amount) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('actual_ifund_amount') border-red-400 @enderror">
                                    @error('actual_ifund_amount')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">Gross Sales (₱)</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="target_gross_sales" min="0" step="0.01"
                                           value="{{ old('target_gross_sales', $setupAccomplishment->target_gross_sales) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('target_gross_sales') border-red-400 @enderror">
                                    @error('target_gross_sales')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="actual_gross_sales" min="0" step="0.01"
                                           value="{{ old('actual_gross_sales', $setupAccomplishment->actual_gross_sales) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('actual_gross_sales') border-red-400 @enderror">
                                    @error('actual_gross_sales')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">Employment Generated</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="target_employment" min="0"
                                           value="{{ old('target_employment', $setupAccomplishment->target_employment) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('target_employment') border-red-400 @enderror">
                                    @error('target_employment')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="actual_employment" min="0"
                                           value="{{ old('actual_employment', $setupAccomplishment->actual_employment) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('actual_employment') border-red-400 @enderror">
                                    @error('actual_employment')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>

                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-700">Trainings Conducted</td>
                                <td class="px-4 py-3">
                                    <input type="number" name="target_trainings" min="0"
                                           value="{{ old('target_trainings', $setupAccomplishment->target_trainings) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('target_trainings') border-red-400 @enderror">
                                    @error('target_trainings')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="actual_trainings" min="0"
                                           value="{{ old('actual_trainings', $setupAccomplishment->actual_trainings) }}"
                                           class="w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-300 @error('actual_trainings') border-red-400 @enderror">
                                    @error('actual_trainings')<p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                @if(auth()->user()->isAdmin())
                <form method="POST"
                      action="{{ route('projects.setup-accomplishments.destroy', [$project, $setupAccomplishment]) }}"
                      onsubmit="return confirm('Delete this record? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50">
                        Delete Record
                    </button>
                </form>
                @else
                <span></span>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.setup-accomplishments.index', $project) }}"
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
</x-app-layout>
