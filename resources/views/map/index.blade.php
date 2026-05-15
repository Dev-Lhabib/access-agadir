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

    <div id="map" class="flex-grow h-64 md:h-full"></div>
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

        init() {
            this.initMap();
            this.loadData();
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