@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div x-data="mapComponent()" class="flex flex-col md:flex-row h-[calc(100vh-64px)]">
    <aside class="w-full md:w-80 bg-white p-4 overflow-y-auto border-r border-gray-200">
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

        <div x-show="loading" class="mt-4 flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-violet-600"></div>
        </div>
    </aside>

    <div id="map" class="flex-grow h-64 md:h-full relative">
        <button @click="obstacleMode = true" class="absolute bottom-8 right-8 z-[1000] px-4 py-3 bg-red-600 text-white rounded-full shadow-lg hover:bg-red-700 transition flex items-center gap-2">
            <span>⚠️</span>
            <span class="hidden sm:inline">Signaler un obstacle</span>
        </button>

        <div x-show="obstacleMode" x-cloak x-transition class="absolute top-4 right-4 z-[1000] w-80 bg-white rounded-xl shadow-2xl p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-red-700">Signaler un obstacle</h3>
                <button @click="obstacleMode = false; obstacleLat = null; obstacleLng = null" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <template x-if="!obstacleLat">
                <p class="text-sm text-gray-600 mb-2">Cliquez sur la carte pour sélectionner la position de l'obstacle.</p>
            </template>

            <template x-if="obstacleLat">
                <form @submit.prevent="submitObstacle">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'obstacle</label>
                        <select x-model="obstacleType" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">Sélectionner...</option>
                            <option value="escalier_bloquant">Escalier bloquant</option>
                            <option value="trottoir_casse">Trottoir cassé</option>
                            <option value="pente_forte">Pente forte</option>
                            <option value="travaux">Travaux</option>
                            <option value="absence_rampe">Absence de rampe</option>
                            <option value="route_dangereuse">Route dangereuse</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="obstacleDesc" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Décrivez l'obstacle..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gravité</label>
                        <select x-model="obstacleSeverity" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="low">Faible</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Haute</option>
                        </select>
                    </div>
                    <button type="submit" :disabled="submittingObstacle" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition">
                        <span x-show="!submittingObstacle">Envoyer le signalement</span>
                        <span x-show="submittingObstacle">Envoi...</span>
                    </button>
                </form>
            </template>

            <div x-show="obstacleSuccess" x-cloak x-transition class="mt-3 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                ✅ Signalement envoyé ! Il sera affiché après modération.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
        obstacleMode: false,
        obstacleLat: null,
        obstacleLng: null,
        obstacleType: '',
        obstacleDesc: '',
        obstacleSeverity: 'medium',
        submittingObstacle: false,
        obstacleSuccess: false,

        init() {
            this.initMap();
            this.loadData();
            this.checkUrlParams();
            this.setupObstacleClick();
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

        setupObstacleClick() {
            this.map.on('click', (e) => {
                if (this.obstacleMode && !this.obstacleLat) {
                    this.obstacleLat = e.latlng.lat;
                    this.obstacleLng = e.latlng.lng;
                }
            });
        },

        async submitObstacle() {
            this.submittingObstacle = true;
            const res = await fetch('{{ route('obstacles.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lat: this.obstacleLat,
                    lng: this.obstacleLng,
                    type: this.obstacleType,
                    description: this.obstacleDesc,
                    severity: this.obstacleSeverity
                })
            });

            const data = await res.json();
            if (data.success) {
                this.obstacleSuccess = true;
                this.obstacleLat = null;
                this.obstacleLng = null;
                this.obstacleType = '';
                this.obstacleDesc = '';
                setTimeout(() => {
                    this.obstacleMode = false;
                    this.obstacleSuccess = false;
                }, 3000);
            }
            this.submittingObstacle = false;
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