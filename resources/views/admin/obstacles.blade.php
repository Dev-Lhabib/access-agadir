@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Modération des obstacles</h1>
            <p class="text-gray-600 mt-1">Gérez les signalements d'obstacles signalés par les utilisateurs</p>
        </div>
        <a href="{{ route('map.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
            ← Retour à la carte
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid gap-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-yellow-50 border-b border-yellow-200">
                <h2 class="text-lg font-semibold text-yellow-800">⏳ En attente de modération ({{ $pendingObstacles->count() }})</h2>
            </div>
            @if($pendingObstacles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Gravité</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Position</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pendingObstacles as $obstacle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                    {{ str_replace('_', ' ', $obstacle->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obstacle->description }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-sm font-medium
                                    @if($obstacle->severity === 'high') bg-red-100 text-red-700
                                    @elseif($obstacle->severity === 'medium') bg-orange-100 text-orange-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $obstacle->severity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ number_format($obstacle->lat, 4) }}, {{ number_format($obstacle->lng, 4) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $obstacle->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('admin.obstacles.update', $obstacle->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm mr-2">Approuver</button>
                                </form>
                                <form action="{{ route('admin.obstacles.update', $obstacle->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Rejeter</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center text-gray-500">Aucun obstacle en attente</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-green-50 border-b border-green-200">
                <h2 class="text-lg font-semibold text-green-800">✅ Approuvés ({{ $approvedObstacles->count() }})</h2>
            </div>
            @if($approvedObstacles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Gravité</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($approvedObstacles as $obstacle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                    {{ str_replace('_', ' ', $obstacle->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obstacle->description }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-sm font-medium
                                    @if($obstacle->severity === 'high') bg-red-100 text-red-700
                                    @elseif($obstacle->severity === 'medium') bg-orange-100 text-orange-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $obstacle->severity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $obstacle->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center text-gray-500">Aucun obstacle approuvé</div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-red-50 border-b border-red-200">
                <h2 class="text-lg font-semibold text-red-800">❌ Rejetés ({{ $rejectedObstacles->count() }})</h2>
            </div>
            @if($rejectedObstacles->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Description</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($rejectedObstacles as $obstacle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                                    {{ str_replace('_', ' ', $obstacle->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $obstacle->description }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $obstacle->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center text-gray-500">Aucun obstacle rejeté</div>
            @endif
        </div>
    </div>
</div>
@endsection