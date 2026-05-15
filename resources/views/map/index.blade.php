@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div x-data="mapComponent()" class="flex flex-col md:flex-row h-[calc(100vh-64px)]">
    <aside class="w-full md:w-80 bg-white p-4 overflow-y-auto border-r border-gray-200 flex flex-col">
        <div x-show="!routeMode">
            <h2 class="text-xl font-bold text-violet-800 mb-4">Filtres</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                    <select x-model="filters.category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" x-model="filters.ramp" class="rounded text-violet-600">
                        <span class="text-sm">Rampe d'accès</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" x-model="filters.adapted_toilet" class="rounded text-violet-600">
                        <span class="text-sm">Toilettes PMR</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" x-model="filters.pmr_parking" class="rounded text-violet-600">
                        <span class="text-sm">Parking PMR</span>
                    </label>
                </div>
            </div>

            <button @click="routeMode = true; initRouteMode()" class="mt-4 w-full px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition">
                🚦 Calculer un itinéraire
            </button>
        </div>

        <div x-show="routeMode" x-cloak class="flex-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-violet-800">Itinéraire</h2>
                <button @click="routeMode = false; clearRoute()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Point de départ</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="departure" placeholder="Adresse ou position actuelle" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <button @click="useMyLocation" class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200" title="Utiliser ma position">
                            📍
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                    <input type="text" x-model="destinationName" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50">
                </div>
            </div>

            <button @click="calculateRoute()" :disabled="!departure || routing" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition">
                <span x-show="!routing">Calculer l'itinéraire</span>
                <span x-show="routing">Calcul en cours...</span>
            </button>

            <div x-show="routeInfo" x-cloak class="mt-4 p-4 bg-violet-50 rounded-lg">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Distance:</span>
                    <span class="font-semibold" x-text="routeInfo.distance"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Durée:</span>
                    <span class="font-semibold" x-text="routeInfo.duration"></span>
                </div>
            </div>

            <div x-show="obstaclesOnRoute.length > 0" x-cloak class="mt-4 p-4 bg-red-50 rounded-lg">
                <h4 class="font-semibold text-red-700 mb-2">⚠️ Obstacles sur le parcours</h4>
                <ul class="text-sm text-red-600 space-y-1">
                    <template x-for="obs in obstaclesOnRoute" :key="obs.id">
                        <li>• <span x-text="obs.type"></span> (<span x-text="obs.severity"></span>)</li>
                    </template>
                </ul>
            </div>
        </div>

        <div x-show="loading" class="mt-4 flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600"></div>
        </div>
    </aside>

    <div id="map" class="flex-grow h-64 md:h-full"></div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script>
function mapComponent() {
    return {
        map: null,
        placeMarkers: [],
        obstacleMarkers: [],
        filters: {
            category_id: '',
            ramp: false,
            adapted_toilet: false,
            pmr_parking: false,
        },
        loading: false,
        places: [],
        obstacles: [],
        routeMode: false,
        departure: '',
        destination: null,
        destinationName: '',
        routing: false,
        routeInfo: null,
        routeLine: null,
        obstaclesOnRoute: [],

        init() {
            this.initMap();
            this.loadData();
            this.checkUrlParams();
        },

        checkUrlParams() {
            const params = new URLSearchParams(window.location.search);
            const destId = params.get('destination');
            if (destId) {
                this.loadDestination(destId);
            }
        },

        async loadDestination(id) {
            const res = await fetch('/api/places');
            const places = await res.json();
            const place = places.find(p => p.id == id);
            if (place) {
                this.destination = [place.lat, place.lng];
                this.destinationName = place.name;
                this.routeMode = true;
                this.initRouteMode();
                this.map.setView(this.destination, 14);
            }
        },

        initRouteMode() {
            if (this.destination) {
                this.map.setView(this.destination, 14);
            }
        },

        useMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.departure = `${pos.coords.latitude},${pos.coords.longitude}`;
                    },
                    () => alert('Impossible de获取 votre position')
                );
            }
        },

        async calculateRoute() {
            if (!this.departure || !this.destination) return;

            this.routing = true;
            this.clearRoute();

            let startCoords;
            if (this.departure.includes(',')) {
                const [lat, lng] = this.departure.split(',').map(Number);
                startCoords = [lat, lng];
            } else {
                const geocode = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.departure + ', Agadir')}`);
                const results = await geocode.json();
                if (results.length === 0) {
                    alert('Adresse non trouvée');
                    this.routing = false;
                    return;
                }
                startCoords = [parseFloat(results[0].lat), parseFloat(results[0].lon)];
            }

            try {
                const routeRes = await fetch(
                    `https://router.project-osrm.org/route/v1/foot/${startCoords[1]},${startCoords[0]};${this.destination[1]},${this.destination[0]}?overview=full&geometries=geojson`
                );
                const routeData = await routeRes.json();

                if (routeData.code !== 'Ok') {
                    alert('Aucun itinéraire trouvé');
                    this.routing = false;
                    return;
                }

                const route = routeData.routes[0];
                const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);

                this.routeLine = L.polyline(coords, { color: '#8b5cf6', weight: 5 }).addTo(this.map);
                this.map.fitBounds(this.routeLine.getBounds(), { padding: [50, 50] });

                const distanceKm = (route.distance / 1000).toFixed(1);
                const durationMin = Math.round(route.duration / 60);
                this.routeInfo = {
                    distance: distanceKm + ' km',
                    duration: durationMin < 60 ? durationMin + ' min' : Math.floor(durationMin / 60) + 'h ' + (durationMin % 60) + 'min'
                };

                this.findObstaclesOnRoute(coords);
            } catch (e) {
                console.error(e);
                alert('Erreur lors du calcul');
            }

            this.routing = false;
        },

        findObstaclesOnRoute(routeCoords) {
            this.obstaclesOnRoute = [];
            const threshold = 0.002;

            this.obstacles.forEach(o => {
                for (let c of routeCoords) {
                    if (Math.abs(o.lat - c[0]) < threshold && Math.abs(o.lng - c[1]) < threshold) {
                        this.obstaclesOnRoute.push(o);
                        break;
                    }
                }
            });
        },

        clearRoute() {
            if (this.routeLine) {
                this.map.removeLayer(this.routeLine);
                this.routeLine = null;
            }
            this.routeInfo = null;
            this.obstaclesOnRoute = [];
        },

        initMap() {
            this.map = L.map('map').setView([30.4278, -9.5981], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);
        },

        async loadData() {
            this.loading = true;

            const [placesRes, obstaclesRes] = await Promise.all([
                fetch('/api/places'),
                fetch('/api/obstacles')
            ]);

            this.places = await placesRes.json();
            this.obstacles = await obstaclesRes.json();

            this.renderPlaceMarkers();
            this.renderObstacleMarkers();
            this.loading = false;
        },

        renderPlaceMarkers() {
            this.placeMarkers.forEach(m => m.remove());
            this.placeMarkers = [];

            const filtered = this.places.filter(p => {
                if (this.filters.category_id && p.category_id !== parseInt(this.filters.category_id)) return false;
                if (this.filters.ramp && !p.ramp) return false;
                if (this.filters.adapted_toilet && !p.adapted_toilet) return false;
                if (this.filters.pmr_parking && !p.pmr_parking) return false;
                return true;
            });

            filtered.forEach(p => {
                const isPmrParking = p.pmr_parking;
                const icon = isPmrParking
                    ? L.divIcon({ className: 'bg-blue-500 w-8 h-8 rounded-full flex items-center justify-center text-white font-bold', html: 'P', iconSize: [32, 32] })
                    : L.divIcon({ className: 'bg-violet-600 w-8 h-8 rounded-full flex items-center justify-center text-white text-xs', html: '📍', iconSize: [32, 32] });

                const marker = L.marker([p.lat, p.lng], { icon }).addTo(this.map);

                const stars = '★'.repeat(Math.floor(p.rating)) + '☆'.repeat(5 - Math.floor(p.rating));
                const accessibilityBadges = [
                    p.wheelchair ? '♿' : '',
                    p.ramp ? '🟢' : '',
                    p.elevator ? '🛗' : '',
                    p.adapted_toilet ? '🚻' : '',
                    p.pmr_parking ? '🅿️' : '',
                ].filter(Boolean).join(' ');

                marker.bindPopup(`
                    <div class="text-center">
                        <h3 class="font-bold">${p.name}</h3>
                        <p class="text-sm text-gray-600">${p.category}</p>
                        <p class="text-yellow-500">${stars}</p>
                        <p class="text-sm mt-1">${accessibilityBadges}</p>
                        <a href="${p.url}" class="inline-block mt-2 text-violet-600 hover:underline">Voir la fiche</a>
                    </div>
                `);

                this.placeMarkers.push(marker);
            });
        },

        renderObstacleMarkers() {
            this.obstacleMarkers.forEach(m => m.remove());
            this.obstacleMarkers = [];

            const severityColors = {
                low: '#fbbf24',
                medium: '#f97316',
                high: '#dc2626'
            };

            this.obstacles.forEach(o => {
                const color = severityColors[o.severity] || '#dc2626';
                const icon = L.divIcon({
                    className: '',
                    html: `<div style="background:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-center;color:white;font-size:14px;">⚠️</div>`,
                    iconSize: [24, 24]
                });

                const marker = L.marker([o.lat, o.lng], { icon }).addTo(this.map);
                marker.bindPopup(`
                    <div>
                        <h3 class="font-bold">Obstacle</h3>
                        <p class="text-sm">Type: ${o.type}</p>
                        <p class="text-sm">Sévérité: ${o.severity}</p>
                    </div>
                `);

                this.obstacleMarkers.push(marker);
            });
        },

        $watch: {
            'filters': {
                handler() {
                    this.renderPlaceMarkers();
                },
                deep: true
            }
        }
    };
}
</script>
@endpush