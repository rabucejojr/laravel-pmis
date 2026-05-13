<x-app-layout :title="$project->title">

    {{-- Breadcrumb + Header --}}
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">
            <a href="{{ route('projects.index', ['program' => $project->program->code]) }}"
               class="hover:underline" style="color:#003087;">
                {{ $project->program->code }}
            </a>
            / {{ $project->title }}
        </p>
        <div class="flex items-start justify-between gap-4">
            <h2 class="text-2xl font-bold leading-tight" style="color:#003087;">{{ $project->title }}</h2>
            @if(auth()->user()->hasRole(['admin', 'encoder']))
            <a href="{{ route('projects.edit', $project) }}"
               class="shrink-0 px-4 py-2 rounded-lg border text-sm font-medium transition"
               style="border-color:#003087;color:#003087;"
               onmouseover="this.style.background='#003087';this.style.color='#fff'"
               onmouseout="this.style.background='';this.style.color='#003087'">
                Edit Project
            </a>
            @endif
        </div>
    </div>

    {{-- Quick Links bar --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <a href="{{ route('projects.financial-targets.index', $project) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Financial Targets
        </a>
        <a href="{{ route('projects.physical-accomplishments.index', $project) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Physical Accomplishments
        </a>
        @if($project->program->code === 'SETUP')
        <a href="{{ route('projects.setup-accomplishments.index', $project) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            SETUP KPI Accomplishments
        </a>
        @endif
        <a href="{{ route('projects.budget.show', $project) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-medium text-gray-600 hover:border-blue-300 hover:text-blue-700 hover:bg-blue-50 transition-colors shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a1 1 0 001-1V6a1 1 0 00-1-1H4a1 1 0 00-1 1v12a1 1 0 001 1z"/>
            </svg>
            Budget Breakdown (full page)
        </a>
    </div>

    <div class="space-y-6">

        {{-- Project Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Project Information</h3>
            <dl class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-400 font-medium">Program</dt>
                    <dd class="mt-0.5 font-semibold" style="color:#003087;">
                        {{ $project->program->code }} — {{ $project->program->name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium">Status</dt>
                    <dd class="mt-0.5">
                        @php
                            $badge = match($project->status) {
                                'active'       => 'bg-green-100 text-green-700',
                                'liquidated'   => 'bg-blue-100 text-blue-700',
                                'unliquidated' => 'bg-amber-100 text-amber-700',
                                default        => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badge }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium">Total Approved Budget</dt>
                    <dd class="mt-0.5 font-semibold text-base" style="color:#003087;">
                        ₱ {{ number_format($project->total_approved_budget, 2) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium">Documents</dt>
                    <dd class="mt-0.5 font-semibold text-base" style="color:#003087;">
                        {{ $project->documents->count() }}
                        <span class="text-xs font-normal text-gray-400">uploaded</span>
                    </dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-400 font-medium">Implementing Agency</dt>
                    <dd class="mt-0.5 font-medium" style="color:#1A1A2E;">{{ $project->implementing_agency }}</dd>
                </div>
                @if($project->co_implementing_agency)
                <div class="col-span-2">
                    <dt class="text-gray-400 font-medium">Co-Implementing Agency</dt>
                    <dd class="mt-0.5 font-medium" style="color:#1A1A2E;">{{ $project->co_implementing_agency }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-gray-400 font-medium">Location</dt>
                    <dd class="mt-0.5">{{ $project->location ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-400 font-medium">Duration</dt>
                    <dd class="mt-0.5">
                        {{ $project->start_date->format('M d, Y') }}
                        <span class="text-gray-400 mx-1">→</span>
                        {{ $project->end_date->format('M d, Y') }}
                    </dd>
                </div>
                @if($project->description)
                <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                    <dt class="text-gray-400 font-medium">Description</dt>
                    <dd class="mt-0.5 text-gray-600 leading-relaxed">{{ $project->description }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Budget Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <livewire:budget-item-editor :project="$project" />
        </div>

        {{-- Project Documents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-4">Project Documents</h3>

            @if(auth()->user()->hasRole(['admin', 'encoder']))
            <form method="POST" action="{{ route('documents.store', $project) }}"
                  enctype="multipart/form-data"
                  class="flex flex-wrap gap-3 items-end p-4 rounded-lg mb-5"
                  style="background-color:#F5F7FA;">
                @csrf
                <div class="flex-1 min-w-36">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Document Type</label>
                    <select name="document_type"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('document_type') border-red-400 @enderror">
                        <option value="">Select type…</option>
                        @foreach(['GAA','financial_plan','work_plan','MOA','other'] as $type)
                            <option value="{{ $type }}" {{ old('document_type') === $type ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($type)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">File</label>
                    <input type="file" name="file"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:text-white"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    @error('file')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-semibold text-white"
                        style="background-color:#003087;">
                    Upload
                </button>
            </form>
            @endif

            @forelse($project->documents as $doc)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                         style="background-color:#003087;">
                        {{ strtoupper(substr($doc->document_type, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium" style="color:#1A1A2E;">{{ $doc->file_name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ str_replace('_', ' ', ucfirst($doc->document_type)) }}
                            &middot; {{ $doc->uploader->name }}
                            &middot; {{ $doc->created_at->format('M d, Y') }}
                            @if($doc->file_size)
                                &middot; {{ number_format($doc->file_size / 1024, 1) }} KB
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('documents.download', $doc) }}"
                       class="text-xs font-medium" style="color:#003087;">Download</a>
                    @if(auth()->user()->hasRole(['admin', 'encoder']))
                    <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                          onsubmit="return confirm('Delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-6">No documents uploaded yet.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
