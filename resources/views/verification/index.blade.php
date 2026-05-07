<x-app-layout title="Verification Queue">
    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-1">Verification</p>
        <h2 class="text-2xl font-bold" style="color:#003087;">Verification Queue</h2>
        <p class="text-sm text-gray-500 mt-1">
            Review pending and flagged entries. Verified entries become visible to all roles.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <livewire:verification-queue />
    </div>
</x-app-layout>
