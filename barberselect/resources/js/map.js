import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl,
    iconUrl,
    shadowUrl,
});

const MAX_RECOMMENDATIONS = 8;
const SEARCH_RADIUS_M = 5000;
const REFETCH_MOVE_METERS = 80;
const REFETCH_INTERVAL_MS = 20000;
const FOLLOW_ZOOM = 15;

const escapeHtml = (value) => {
    const el = document.createElement('div');
    el.textContent = value ?? '';
    return el.innerHTML;
};

const toRadians = (value) => (value * Math.PI) / 180;

const haversineDistance = (lat1, lng1, lat2, lng2) => {
    const earthRadiusKm = 6371;
    const dLat = toRadians(lat2 - lat1);
    const dLng = toRadians(lng2 - lng1);
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.sin(dLng / 2) ** 2;

    return earthRadiusKm * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
};

const formatDistanceKm = (distanceKm) => {
    if (distanceKm == null || Number.isNaN(distanceKm)) return null;
    if (distanceKm < 1) return `${distanceKm.toFixed(2)} km`;
    if (distanceKm < 10) return `${distanceKm.toFixed(2)} km`;
    return `${distanceKm.toFixed(1)} km`;
};

const formatRating = (rating) => {
    const value = parseFloat(rating);
    if (Number.isNaN(value)) return null;
    return `★ ${value.toFixed(1)}`;
};

const distanceMovedMeters = (from, to) => {
    if (!from || !to) return Infinity;
    return haversineDistance(from.lat, from.lng, to.lat, to.lng) * 1000;
};

const rankLabel = (index) => (index === 0 ? 'Terdekat' : `#${index + 1}`);

const buildLoadingList = () => `
    <div class="space-y-3">
        ${[1, 2, 3].map(() => `
            <div class="animate-pulse rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <div class="h-4 w-2/3 rounded bg-white/10"></div>
                <div class="mt-3 h-3 w-1/3 rounded bg-white/10"></div>
                <div class="mt-2 h-3 w-full rounded bg-white/10"></div>
            </div>
        `).join('')}
    </div>
`;

const buildDeniedList = (message) => `
    <div class="rounded-2xl border border-amber-400/25 bg-amber-400/10 p-5">
        <p class="text-sm font-semibold text-amber-200">Izin lokasi diperlukan</p>
        <p class="mt-2 text-sm leading-relaxed text-amber-100/80">${escapeHtml(message)}</p>
        <button type="button" id="retryLocationBtn" class="mt-4 w-full rounded-full bg-white px-4 py-2.5 text-xs font-semibold text-neutral-950 hover:bg-white/90">
            Izinkan lokasi & coba lagi
        </button>
        <p class="mt-3 text-xs text-amber-100/60">Buka pengaturan browser → Izin → Lokasi → Allow.</p>
    </div>
`;

const buildPopupHtml = (shop) => `
    <div style="min-width:200px;line-height:1.5">
        <strong>${escapeHtml(shop.name)}</strong>
        ${shop.source === 'partner' ? '<br><span style="color:#10b981;font-size:11px;font-weight:600">Partner BarberSelect</span>' : ''}
        ${shop.rating_label ? `<br><span style="color:#f59e0b;font-size:12px;font-weight:600">${escapeHtml(shop.rating_label)}</span>` : ''}
        ${shop.distance_label ? `<br><span style="color:#2563eb;font-size:12px;font-weight:600">${escapeHtml(shop.distance_label)} dari kamu</span>` : ''}
        ${shop.address ? `<br><span style="opacity:.85;font-size:13px">${escapeHtml(shop.address)}</span>` : ''}
        ${shop.hours ? `<br><span style="opacity:.75;font-size:12px">Jam buka: ${escapeHtml(shop.hours)}</span>` : ''}
    </div>
`;

const enrichShop = (shop, userLocation) => {
    const distanceKm =
        shop.distance_km ??
        haversineDistance(userLocation.lat, userLocation.lng, shop.lat, shop.lng);

    return {
        ...shop,
        id: String(shop.id),
        lat: parseFloat(shop.lat),
        lng: parseFloat(shop.lng),
        distance_km: distanceKm,
        distance_label: formatDistanceKm(distanceKm),
        rating_label: formatRating(shop.rating),
    };
};

const filterRecommendedShops = (sortedShops) => {
    if (sortedShops.length === 0) return [];

    const nearestKm = sortedShops[0].distance_km;
    if (nearestKm > 5) {
        return sortedShops.slice(0, MAX_RECOMMENDATIONS);
    }

    const maxRadiusKm = Math.max(nearestKm * 2.5, nearestKm + 1);
    return sortedShops
        .filter((shop) => shop.distance_km <= maxRadiusKm)
        .slice(0, MAX_RECOMMENDATIONS);
};

const initBarberMap = () => {
    const mapEl = document.getElementById('barberMap');
    const listEl = document.getElementById('barberList');
    const statusEl = document.getElementById('mapStatus');
    const loadingEl = document.getElementById('mapLoading');
    const followBtn = document.getElementById('followLocationBtn');
    const liveIndicator = document.getElementById('liveIndicator');
    if (!mapEl || !listEl) return;

    const nearbyUrl = mapEl.dataset.nearbyUrl || '/nearby-barbers';
    const defaultLat = parseFloat(mapEl.dataset.centerLat) || -6.2088;
    const defaultLng = parseFloat(mapEl.dataset.centerLng) || 106.8456;

    let watchId = null;
    let lastUserLocation = null;
    let lastFetchLocation = null;
    let lastFetchTime = 0;
    let followUser = true;
    let hasInitialFit = false;
    let isFetching = false;
    let markerById = new Map();
    let userMarker = null;
    let userPulse = null;
    let userAccuracyCircle = null;
    let searchRadiusCircle = null;

    const setStatus = (text, type = 'info') => {
        if (!statusEl) return;
        statusEl.textContent = text;
        statusEl.className = 'text-sm';
        if (type === 'error') statusEl.classList.add('text-red-300');
        else if (type === 'success') statusEl.classList.add('text-emerald-200');
        else if (type === 'loading') statusEl.classList.add('text-sky-300');
        else statusEl.classList.add('text-white/65');
    };

    const setMapLoading = (isLoading, label = 'Mengambil lokasi GPS...') => {
        if (!loadingEl) return;
        loadingEl.classList.toggle('hidden', !isLoading);
        const labelEl = loadingEl.querySelector('[data-loading-label]');
        if (labelEl) labelEl.textContent = label;
    };

    const setLive = (active) => {
        if (!liveIndicator) return;
        liveIndicator.classList.toggle('hidden', !active);
    };

    const map = L.map('barberMap', {
        scrollWheelZoom: true,
        zoomControl: true,
    }).setView([defaultLat, defaultLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    map.on('dragstart', () => {
        followUser = false;
        if (followBtn) {
            followBtn.textContent = 'Ikuti lokasi';
            followBtn.classList.remove('bg-sky-500', 'text-white');
            followBtn.classList.add('bg-white/10', 'text-white/80');
        }
    });

    const markerLayer = L.layerGroup().addTo(map);

    const clearBarberMarkers = () => {
        markerLayer.eachLayer((layer) => {
            if (![userMarker, userPulse, userAccuracyCircle, searchRadiusCircle].includes(layer)) {
                markerLayer.removeLayer(layer);
            }
        });
        markerById.clear();
    };

    const updateUserMarker = (userLocation) => {
        const point = [userLocation.lat, userLocation.lng];

        if (!userMarker) {
            userPulse = L.circleMarker(point, {
                radius: 16,
                color: '#3b82f6',
                weight: 1,
                fillColor: '#3b82f6',
                fillOpacity: 0.2,
                className: 'user-pulse-marker',
            }).addTo(markerLayer);

            userMarker = L.circleMarker(point, {
                radius: 8,
                color: '#ffffff',
                weight: 3,
                fillColor: '#3b82f6',
                fillOpacity: 1,
            }).addTo(markerLayer);
            userMarker.bindPopup('<strong>Lokasi kamu</strong><br><span style="font-size:12px;opacity:.8">Live tracking aktif</span>');
        } else {
            userMarker.setLatLng(point);
            if (userPulse) userPulse.setLatLng(point);
        }

        if (userLocation.accuracy && userLocation.accuracy < 800) {
            if (!userAccuracyCircle) {
                userAccuracyCircle = L.circle(point, {
                    radius: userLocation.accuracy,
                    color: '#3b82f6',
                    weight: 1,
                    fillColor: '#3b82f6',
                    fillOpacity: 0.08,
                }).addTo(markerLayer);
            } else {
                userAccuracyCircle.setLatLng(point);
                userAccuracyCircle.setRadius(userLocation.accuracy);
            }
        }

        if (!searchRadiusCircle) {
            searchRadiusCircle = L.circle(point, {
                radius: SEARCH_RADIUS_M,
                color: '#38bdf8',
                weight: 1,
                dashArray: '6 8',
                fillColor: '#38bdf8',
                fillOpacity: 0.04,
            }).addTo(markerLayer);
        } else {
            searchRadiusCircle.setLatLng(point);
        }
    };

    const renderBarberMarkers = (shops) => {
        clearBarberMarkers();
        shops.forEach((shop) => {
            const marker = L.marker([shop.lat, shop.lng]).addTo(markerLayer);
            marker.bindPopup(buildPopupHtml(shop));
            markerById.set(shop.id, marker);
        });
    };

    const setActiveListItem = (shopId) => {
        listEl.querySelectorAll('[data-shop-id]').forEach((item) => {
            const isActive = item.dataset.shopId === String(shopId);
            item.classList.toggle('border-white/30', isActive);
            item.classList.toggle('bg-white/10', isActive);
            item.classList.toggle('border-white/10', !isActive);
            item.classList.toggle('bg-white/[0.03]', !isActive);
        });
    };

    const focusShop = (shopId) => {
        const marker = markerById.get(String(shopId));
        if (!marker) return;
        followUser = false;
        map.flyTo(marker.getLatLng(), 16, { duration: 0.7 });
        marker.openPopup();
        setActiveListItem(shopId);
    };

    const renderRecommendations = (shops) => {
        if (shops.length === 0) {
            listEl.innerHTML = `
                <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-sm text-white/60">
                    Tidak ada barbershop dalam radius ${SEARCH_RADIUS_M / 1000} km. Coba pindah lokasi atau perbesar area pencarian.
                </div>
            `;
            return;
        }

        listEl.innerHTML = shops
            .map((shop, index) => {
                const label = rankLabel(index);
                const badgeClass =
                    index === 0
                        ? 'bg-emerald-400/20 text-emerald-200 ring-emerald-400/30'
                        : 'bg-white/10 text-white/70 ring-white/15';

                return `
                    <button type="button" data-shop-id="${escapeHtml(shop.id)}"
                        class="rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-left transition hover:bg-white/[0.06]">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold text-white">${escapeHtml(shop.name)}</p>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold ring-1 ${badgeClass}">${label}</span>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-semibold text-sky-300">${escapeHtml(shop.distance_label)}</span>
                            ${shop.rating_label ? `<span class="text-amber-300">${escapeHtml(shop.rating_label)}</span>` : ''}
                            ${shop.source === 'partner' ? '<span class="text-emerald-300">Partner</span>' : '<span class="text-white/40">OSM</span>'}
                        </div>
                        ${shop.address ? `<p class="mt-2 text-xs leading-relaxed text-white/60">${escapeHtml(shop.address)}</p>` : ''}
                        ${shop.hours ? `<p class="mt-1 text-xs text-white/45">Jam buka: ${escapeHtml(shop.hours)}</p>` : ''}
                    </button>
                `;
            })
            .join('');

        listEl.querySelectorAll('[data-shop-id]').forEach((button) => {
            button.addEventListener('click', () => focusShop(button.dataset.shopId));
        });
    };

    const fetchNearbyBarbers = async (userLocation) => {
        const url = new URL(nearbyUrl, window.location.origin);
        url.searchParams.set('lat', userLocation.lat);
        url.searchParams.set('lng', userLocation.lng);
        url.searchParams.set('radius', String(SEARCH_RADIUS_M));

        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Gagal mengambil barbershop di sekitar lokasi kamu.');

        const payload = await response.json();
        return Array.isArray(payload.data) ? payload.data : [];
    };

    const shouldRefetch = (userLocation) => {
        const now = Date.now();
        if (!lastFetchLocation) return true;
        if (now - lastFetchTime >= REFETCH_INTERVAL_MS) return true;
        return distanceMovedMeters(lastFetchLocation, userLocation) >= REFETCH_MOVE_METERS;
    };

    const applyNearbyResults = (shops, userLocation, { forceFit = false } = {}) => {
        const enriched = shops
            .map((shop) => enrichShop(shop, userLocation))
            .sort((a, b) => a.distance_km - b.distance_km);
        const recommended = filterRecommendedShops(enriched);

        renderBarberMarkers(recommended);
        renderRecommendations(recommended);

        if (forceFit && recommended.length > 0) {
            const bounds = L.latLngBounds(recommended.map((s) => [s.lat, s.lng]));
            bounds.extend([userLocation.lat, userLocation.lng]);
            map.fitBounds(bounds.pad(0.12));
            hasInitialFit = true;
        }
    };

    const updateFromLocation = async (userLocation, { forceFit = false } = {}) => {
        updateUserMarker(userLocation);

        if (followUser) {
            map.setView([userLocation.lat, userLocation.lng], map.getZoom() < FOLLOW_ZOOM ? FOLLOW_ZOOM : map.getZoom(), {
                animate: true,
                duration: 0.4,
            });
        }

        if (!shouldRefetch(userLocation) && !forceFit) {
            if (lastUserLocation) {
                const shops = [...markerById.keys()];
                if (shops.length > 0) {
                    setStatus(`Live · ${shops.length} barbershop di sekitar · GPS diperbarui`, 'success');
                }
            }
            lastUserLocation = userLocation;
            return;
        }

        if (isFetching) {
            lastUserLocation = userLocation;
            return;
        }

        isFetching = true;
        setStatus('Mencari barbershop di sekitar lokasi kamu...', 'loading');

        try {
            const shops = await fetchNearbyBarbers(userLocation);
            applyNearbyResults(shops, userLocation, { forceFit: forceFit || !hasInitialFit });
            lastFetchLocation = { ...userLocation };
            lastFetchTime = Date.now();
            setStatus(`Live · ${shops.length} barbershop ditemukan dalam radius ${SEARCH_RADIUS_M / 1000} km`, 'success');
            setLive(true);
        } catch (error) {
            setStatus(error.message || 'Gagal memuat barbershop terdekat.', 'error');
        } finally {
            isFetching = false;
            lastUserLocation = userLocation;
            setMapLoading(false);
        }
    };

    const handlePositionUpdate = (position) => {
        const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
            accuracy: position.coords.accuracy,
        };

        updateFromLocation(userLocation, { forceFit: !hasInitialFit });
    };

    const stopWatching = () => {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
        setLive(false);
    };

    const showLocationDenied = (message) => {
        stopWatching();
        setMapLoading(false);
        clearBarberMarkers();
        [userMarker, userPulse, userAccuracyCircle, searchRadiusCircle].forEach((layer) => {
            if (layer) markerLayer.removeLayer(layer);
        });
        userMarker = userPulse = userAccuracyCircle = searchRadiusCircle = null;
        listEl.innerHTML = buildDeniedList(message);
        setStatus(message, 'error');
        document.getElementById('retryLocationBtn')?.addEventListener('click', () => startTracking());
    };

    const startTracking = () => {
        if (!navigator.geolocation) {
            showLocationDenied('Browser kamu tidak mendukung Geolocation API.');
            return;
        }

        stopWatching();
        hasInitialFit = false;
        lastUserLocation = null;
        lastFetchLocation = null;
        lastFetchTime = 0;
        followUser = true;
        listEl.innerHTML = buildLoadingList();
        setMapLoading(true, 'Meminta izin lokasi GPS...');
        setStatus('Meminta izin lokasi GPS realtime...', 'loading');

        watchId = navigator.geolocation.watchPosition(
            handlePositionUpdate,
            (error) => {
                const messages = {
                    1: 'Izin lokasi ditolak. Aktifkan GPS untuk melihat barbershop di sekitar secara live.',
                    2: 'Lokasi GPS tidak tersedia. Pastikan GPS aktif.',
                    3: 'Permintaan lokasi habis waktu. Coba lagi.',
                };
                showLocationDenied(messages[error.code] || 'Gagal mengambil lokasi GPS.');
            },
            {
                enableHighAccuracy: true,
                timeout: 25000,
                maximumAge: 0,
            }
        );
    };

    if (followBtn) {
        followBtn.addEventListener('click', () => {
            followUser = !followUser;
            if (followUser) {
                followBtn.textContent = 'Mengikuti';
                followBtn.classList.add('bg-sky-500', 'text-white');
                followBtn.classList.remove('bg-white/10', 'text-white/80');
                if (lastUserLocation) {
                    map.setView([lastUserLocation.lat, lastUserLocation.lng], FOLLOW_ZOOM, { animate: true });
                }
            } else {
                followBtn.textContent = 'Ikuti lokasi';
                followBtn.classList.remove('bg-sky-500', 'text-white');
                followBtn.classList.add('bg-white/10', 'text-white/80');
            }
        });
    }

    listEl.innerHTML = buildLoadingList();
    setMapLoading(true, 'Menyiapkan live tracking...');
    setStatus('Menyiapkan live tracking seperti Google Maps...', 'loading');
    startTracking();

    window.addEventListener('resize', () => map.invalidateSize());
    window.addEventListener('beforeunload', stopWatching);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBarberMap);
} else {
    initBarberMap();
}
