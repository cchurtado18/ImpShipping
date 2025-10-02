<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Followups') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Client Followups</h1>
                                <p class="text-gray-600">Manage client follow-up schedules and notes</p>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('clients.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    ← Back to Clients
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Followups (Red) -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Overdue Followups ({{ $overdueClients->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($overdueClients->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($overdueClients as $client)
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $client->full_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $client->us_phone }}</p>
                                                <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                            </div>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                                {{ $client->next_followup_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        
                                        @if($client->followup_note)
                                            <p class="text-sm text-gray-700 mb-3 bg-white p-2 rounded border">
                                                <strong>Note:</strong> {{ $client->followup_note }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('followups.postpone', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    +7 Days
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No overdue followups</h3>
                                <p class="mt-1 text-sm text-gray-500">All clients are up to date!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Today's Followups (Yellow) -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="bg-yellow-50 px-6 py-4 border-b border-yellow-200">
                        <h3 class="text-lg font-semibold text-yellow-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Today's Followups ({{ $todayClients->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($todayClients->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($todayClients as $client)
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $client->full_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $client->us_phone }}</p>
                                                <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                            </div>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                                Today
                                            </span>
                                        </div>
                                        
                                        @if($client->followup_note)
                                            <p class="text-sm text-gray-700 mb-3 bg-white p-2 rounded border">
                                                <strong>Note:</strong> {{ $client->followup_note }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('followups.postpone', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    +7 Days
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No followups for today</h3>
                                <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Followups (Green) -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                        <h3 class="text-lg font-semibold text-green-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Upcoming Followups - Next 7 Days ({{ $upcomingClients->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($upcomingClients->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($upcomingClients as $client)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $client->full_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $client->us_phone }}</p>
                                                <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                            </div>
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                                {{ $client->next_followup_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                        
                                        @if($client->followup_note)
                                            <p class="text-sm text-gray-700 mb-3 bg-white p-2 rounded border">
                                                <strong>Note:</strong> {{ $client->followup_note }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('followups.postpone', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    +7 Days
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming followups</h3>
                                <p class="mt-1 text-sm text-gray-500">No followups scheduled for the next 7 days.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
