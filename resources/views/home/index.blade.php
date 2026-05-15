@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)]">
    <section class="relative bg-gradient-to-br from-violet-600 via-violet-700 to-indigo-800 text-white overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.05%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-30"></div>
        
        <div class="max-w-6xl mx-auto px-4 py-20 md:py-32 relative">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-full mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-4">
                    AccessAgadir
                </h1>
                <p class="text-xl md:text-2xl text-violet-100 mb-8 max-w-2xl mx-auto">
                    Discover Agadir's accessible places. Navigate with confidence using our interactive map designed for everyone.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/map" class="inline-flex items-center justify-center px-8 py-4 bg-white text-violet-700 rounded-xl font-semibold text-lg hover:bg-violet-50 transition shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Explorer la carte
                    </a>
                    <a href="/map?destination=1" class="inline-flex items-center justify-center px-8 py-4 bg-violet-500 text-white rounded-xl font-semibold text-lg hover:bg-violet-400 transition border border-violet-400">
                        ♿ Voir lieu accessible
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Pourquoi utiliser AccessAgadir ?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6 rounded-xl bg-violet-50">
                    <div class="w-14 h-14 bg-violet-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🗺️</div>
                    <h3 class="text-xl font-semibold mb-2">Carte Interactive</h3>
                    <p class="text-gray-600">Explorez tous les lieux accessibles d'Agadir avec nos filtres PMR.</p>
                </div>
                <div class="text-center p-6 rounded-xl bg-violet-50">
                    <div class="w-14 h-14 bg-violet-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🤖</div>
                    <h3 class="text-xl font-semibold mb-2">Assistant IA</h3>
                    <p class="text-gray-600">Obtenez des recommandations personnalisées sur l'accessibilité.</p>
                </div>
                <div class="text-center p-6 rounded-xl bg-violet-50">
                    <div class="w-14 h-14 bg-violet-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">⚠️</div>
                    <h3 class="text-xl font-semibold mb-2">Signalement</h3>
                    <p class="text-gray-600">Aidez la communauté en signalant les obstacles que vous rencontrez.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Prêt à explorer Agadir ?</h2>
            <p class="text-gray-600 mb-8">Rejoignez notre communauté pour une ville plus accessible</p>
            <a href="/map" class="inline-block bg-violet-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-violet-700 transition shadow-lg">
                Commencer maintenant
            </a>
        </div>
    </section>
</div>
@endsection