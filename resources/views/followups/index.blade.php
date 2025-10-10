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
                                <button onclick="openScheduleModal()" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Schedule Followup
                                </button>
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
                                                @if($client->email)
                                                    <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-sm text-red-600 font-medium">
                                                Overdue since: {{ $client->next_followup_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($client->followup_notes)
                                                <p class="text-sm text-gray-600 mt-1">{{ $client->followup_notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <button onclick="openPostponeModal({{ $client->id }})" 
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                +24h
                                            </button>
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
                                                @if($client->email)
                                                    <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-sm text-yellow-600 font-medium">
                                                Due: {{ $client->next_followup_at->format('H:i') }}
                                            </p>
                                            @if($client->followup_notes)
                                                <p class="text-sm text-gray-600 mt-1">{{ $client->followup_notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <button onclick="openPostponeModal({{ $client->id }})" 
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                +24h
                                            </button>
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
                                                @if($client->email)
                                                    <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-sm text-green-600 font-medium">
                                                Due: {{ $client->next_followup_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($client->followup_notes)
                                                <p class="text-sm text-gray-600 mt-1">{{ $client->followup_notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('followups.mark-done', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    ✓ Done
                                                </button>
                                            </form>
                                            <button onclick="openPostponeModal({{ $client->id }})" 
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                +24h
                                            </button>
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

            <!-- Completed Followups (Gray) -->
            <div class="mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-gray-500">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Completed Followups ({{ $completedClients->count() }})
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($completedClients->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($completedClients as $client)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $client->full_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $client->us_phone }}</p>
                                                @if($client->email)
                                                    <p class="text-sm text-gray-600">{{ $client->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="text-sm text-gray-600 font-medium">
                                                Completed: {{ $client->last_contacted_at->format('M d, Y H:i') }}
                                            </p>
                                            @if($client->followup_notes)
                                                <p class="text-sm text-gray-600 mt-1">{{ $client->followup_notes }}</p>
                                            @endif
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            <button onclick="openScheduleModal({{ $client->id }})" 
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                ↻ Reschedule
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No completed followups</h3>
                                <p class="mt-1 text-sm text-gray-500">Completed followups will appear here.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Schedule Followup Modal -->
    <div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-40 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-0 w-11/12 md:w-3/4 lg:w-1/2 xl:w-2/5 shadow-2xl rounded-2xl bg-white border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-3 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 p-1.5 rounded-lg mr-2">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Schedule Followup</h3>
                    </div>
                    <button onclick="closeScheduleModal()" class="text-white hover:text-blue-100 transition-colors duration-150">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-5">
                <form method="POST" action="{{ route('followups.schedule') }}" class="space-y-4">
                    @csrf
                    
                    <!-- Client Selection -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Client Selection
                        </h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Select Client <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="client_id" required 
                                        class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150 bg-white appearance-none">
                                    <option value="">Choose a client...</option>
                                    @foreach($allClients as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }} - {{ $client->us_phone }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reminder Time -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Reminder Configuration
                        </h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Reminder in Hours <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="hours" required 
                                        class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150 bg-white appearance-none">
                                    <option value="1">1 hour</option>
                                    <option value="2">2 hours</option>
                                    <option value="4">4 hours</option>
                                    <option value="8">8 hours</option>
                                    <option value="12">12 hours</option>
                                    <option value="24" selected>24 hours (1 day)</option>
                                    <option value="48">48 hours (2 days)</option>
                                    <option value="72">72 hours (3 days)</option>
                                    <option value="168">168 hours (1 week)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-800 flex items-center">
                            <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Additional Information
                        </h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg class="h-3.5 w-3.5 text-gray-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Notes (Optional)
                            </label>
                            <textarea name="notes" rows="2" 
                                      class="w-full px-2.5 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150 resize-none"
                                      placeholder="Additional notes about this follow-up..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-3">
                        <button type="button" onclick="closeScheduleModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all duration-150">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-150 shadow-sm">
                            <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Postpone Modal -->
    <div id="postponeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Postpone Followup</h3>
                    <button onclick="closePostponeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="postponeForm" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postpone by Hours</label>
                        <select name="hours" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">1 hour</option>
                            <option value="2">2 hours</option>
                            <option value="4">4 hours</option>
                            <option value="8">8 hours</option>
                            <option value="12">12 hours</option>
                            <option value="24" selected>24 hours (1 day)</option>
                            <option value="48">48 hours (2 days)</option>
                            <option value="72">72 hours (3 days)</option>
                            <option value="168">168 hours (1 week)</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePostponeModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            Postpone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openScheduleModal(clientId = null) {
            const modal = document.getElementById('scheduleModal');
            const clientSelect = document.querySelector('select[name="client_id"]');
            
            // Preselect client if provided
            if (clientId) {
                clientSelect.value = clientId;
            }
            
            modal.classList.remove('hidden');
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }


        function openPostponeModal(clientId) {
            const form = document.getElementById('postponeForm');
            form.action = `/followups/${clientId}/postpone`;
            document.getElementById('postponeModal').classList.remove('hidden');
        }

        function closePostponeModal() {
            document.getElementById('postponeModal').classList.add('hidden');
        }


        // Close modals when clicking outside
        window.onclick = function(event) {
            const scheduleModal = document.getElementById('scheduleModal');
            const postponeModal = document.getElementById('postponeModal');
            
            if (event.target === scheduleModal) {
                closeScheduleModal();
            }
            if (event.target === postponeModal) {
                closePostponeModal();
            }
        }
    </script>
</x-app-layout>