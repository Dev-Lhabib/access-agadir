@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('map.index') }}" class="inline-flex items-center text-violet-600 hover:text-violet-700 mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à la carte
    </a>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-violet-100 text-violet-700 rounded-full text-sm font-medium mb-2">
                        {{ $place->category->name }}
                    </span>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $place->name }}</h1>
                    <p class="text-gray-500 mt-1">{{ $place->address }}</p>
                </div>
                @if($place->rating)
                <div class="flex items-center bg-yellow-50 px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-semibold text-gray-700">{{ number_format($place->rating, 1) }}</span>
                </div>
                @endif
            </div>

            @if($place->description)
            <p class="text-gray-600 mb-8">{{ $place->description }}</p>
            @endif

            <div id="mini-map" class="w-full h-64 rounded-lg mb-8 shadow-inner"></div>

            <h2 class="text-xl font-semibold text-gray-900 mb-4">Équipements accessibles</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                @if($place->wheelchair)
                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Fauteuil roulant</span>
                </div>
                @endif
                @if($place->ramp)
                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Rampe d'accès</span>
                </div>
                @endif
                @if($place->elevator)
                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Ascenseur</span>
                </div>
                @endif
                @if($place->adapted_toilet)
                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Toilette adaptée</span>
                </div>
                @endif
                @if($place->pmr_parking)
                <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Parking PMR</span>
                </div>
                @endif

                @if(!$place->wheelchair && !$place->ramp && !$place->elevator && !$place->adapted_toilet && !$place->pmr_parking)
                <div class="col-span-full p-4 bg-gray-50 rounded-lg text-gray-500 text-center">
                    Aucun équipement.accessible signalé
                </div>
                @endif
            </div>

            <div class="border-t pt-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Avis</h2>
                    <a href="{{ route('map.index') }}?destination={{ $place->id }}" class="inline-flex items-center px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Tracer l'itinéraire
                    </a>
                </div>

                <div x-data="{ showForm: false, rating: 0, submitting: false, success: false }">
                    <button x-show="!showForm" @click="showForm = true" class="mb-4 px-4 py-2 bg-violet-100 text-violet-700 rounded-lg hover:bg-violet-200 transition">
                        + Ajouter un avis
                    </button>

                    <form x-show="showForm" @submit.prevent="submitting = true; fetch('{{ route('places.reviews.store', $place->id) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ rating: rating, comment: $refs.comment.value }) }).then(() => { success = true; showForm = false; submitting = false; setTimeout(() => location.reload(), 1000) })" class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                            <div class="flex gap-2">
                                <template x-for="i in 5">
                                    <button type="button" @click="rating = i" class="focus:outline-none">
                                        <svg class="w-8 h-8 transition-colors" :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                            <textarea x-ref="comment" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500" placeholder="Votre avis..."></textarea>
                        </div>
                        <button type="submit" :disabled="rating === 0 || submitting" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 disabled:opacity-50 transition">
                            <span x-show="!submitting">Envoyer</span>
                            <span x-show="submitting">Envoi...</span>
                        </button>
                    </form>

                    <div x-show="success" x-transition class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        Avis ajouté avec succès !
                    </div>
                </div>

                @if($place->reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($place->reviews as $review)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if($review->comment)
                        <p class="text-gray-700">{{ $review->comment }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500">Aucun avis pour le moment.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('mini-map').setView([{{ $place->lat }}, {{ $place->lng }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    L.marker([{{ $place->lat }}, {{ $place->lng }}]).addTo(map)
        .bindPopup('{{ $place->name }}')
        .openPopup();
});
</script>
@endpush
@endsection