import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';
import 'package:latlong2/latlong.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/main_bottom_nav.dart';
import '../../data/barber_shop_dto.dart';
import '../../data/map_repository.dart';

class NearbyMapPage extends StatefulWidget {
  const NearbyMapPage({super.key});

  @override
  State<NearbyMapPage> createState() => _NearbyMapPageState();
}

class _NearbyMapPageState extends State<NearbyMapPage> {
  static const int _maxRecommendations = 8;
  static const int _searchRadiusMeters = 5000;
  static const double _refetchMoveMeters = 80;
  static const Duration _refetchInterval = Duration(seconds: 20);
  static const double _followZoom = 15;

  final MapRepository _mapRepo = GetIt.instance<MapRepository>();
  final MapController _mapController = MapController();

  StreamSubscription<Position>? _positionSubscription;
  Position? _position;
  Position? _lastFetchPosition;
  DateTime? _lastFetchAt;
  List<BarberShopDto> _shops = [];
  String? _selectedShopId;
  LocationAccessStatus? _locationAccessStatus;
  String _statusText = 'Menyiapkan live tracking seperti Google Maps...';
  String _statusType = 'loading';
  bool _isLoading = true;
  bool _isFetching = false;
  bool _locationDenied = false;
  bool _followUser = true;
  bool _hasInitialFit = false;
  bool _mapReady = false;

  @override
  void initState() {
    super.initState();
    unawaited(_startTracking());
  }

  Future<void> _startTracking() async {
    await _positionSubscription?.cancel();
    if (!mounted) return;

    setState(() {
      _isLoading = true;
      _locationDenied = false;
      _followUser = true;
      _hasInitialFit = false;
      _lastFetchPosition = null;
      _lastFetchAt = null;
      _statusText = 'Meminta izin lokasi GPS realtime...';
      _statusType = 'loading';
    });

    final access = await _mapRepo.ensureLocationAccess();
    if (!mounted) return;
    _locationAccessStatus = access;

    if (access != LocationAccessStatus.granted) {
      _showLocationError(access);
      return;
    }

    _positionSubscription = _mapRepo.watchCurrentLocation().listen(
      _handlePosition,
      onError: (_) {
        if (!mounted) return;
        _showLocationError(LocationAccessStatus.serviceDisabled);
      },
    );
  }

  void _showLocationError(LocationAccessStatus access) {
    final message = switch (access) {
      LocationAccessStatus.serviceDisabled =>
        'Lokasi GPS tidak tersedia. Aktifkan layanan lokasi lalu coba lagi.',
      LocationAccessStatus.deniedForever =>
        'Izin lokasi diblokir. Aktifkan izin lokasi dari pengaturan aplikasi.',
      _ =>
        'Izin lokasi ditolak. Aktifkan GPS untuk melihat barbershop di sekitar secara live.',
    };

    setState(() {
      _locationAccessStatus = access;
      _locationDenied = true;
      _isLoading = false;
      _shops = [];
      _position = null;
      _statusText = message;
      _statusType = 'error';
    });
  }

  void _handlePosition(Position position) {
    if (!mounted) return;

    setState(() {
      _position = position;
      _locationDenied = false;
    });

    if (_followUser && _mapReady) {
      _moveToUser();
    }

    final forceFit = !_hasInitialFit;
    if (forceFit || _shouldRefetch(position)) {
      unawaited(_fetchNearby(position, forceFit: forceFit));
    } else {
      setState(() {
        _statusText =
            'Live | ${_shops.length} rekomendasi di sekitar | GPS diperbarui';
        _statusType = 'success';
      });
    }
  }

  bool _shouldRefetch(Position position) {
    final lastPosition = _lastFetchPosition;
    final lastFetchAt = _lastFetchAt;
    if (lastPosition == null || lastFetchAt == null) return true;
    if (DateTime.now().difference(lastFetchAt) >= _refetchInterval) return true;

    return Geolocator.distanceBetween(
          lastPosition.latitude,
          lastPosition.longitude,
          position.latitude,
          position.longitude,
        ) >=
        _refetchMoveMeters;
  }

  Future<void> _fetchNearby(Position position, {required bool forceFit}) async {
    if (_isFetching) return;
    _isFetching = true;

    setState(() {
      _statusText = 'Mencari barbershop di sekitar lokasi kamu...';
      _statusType = 'loading';
    });

    try {
      final nearby = await _mapRepo.fetchNearbyBarbers(
        lat: position.latitude,
        lng: position.longitude,
        radiusMeters: _searchRadiusMeters,
      );
      if (!mounted) return;

      final recommendations = _filterRecommendations(nearby);
      setState(() {
        _shops = recommendations;
        _selectedShopId = recommendations.any((s) => s.id == _selectedShopId)
            ? _selectedShopId
            : null;
        _lastFetchPosition = position;
        _lastFetchAt = DateTime.now();
        _isLoading = false;
        _hasInitialFit = true;
        _statusText =
            'Live | ${nearby.length} barbershop ditemukan dalam radius 5 km';
        _statusType = 'success';
      });

      if (forceFit) {
        WidgetsBinding.instance.addPostFrameCallback((_) => _fitInitialView());
      }
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _isLoading = false;
        _statusText = 'Gagal memuat barbershop terdekat.';
        _statusType = 'error';
      });
    } finally {
      _isFetching = false;
    }
  }

  List<BarberShopDto> _filterRecommendations(List<BarberShopDto> shops) {
    final sorted = [...shops]
      ..sort((a, b) => a.distanceKm.compareTo(b.distanceKm));
    if (sorted.isEmpty) return sorted;

    final nearestKm = sorted.first.distanceKm;
    if (nearestKm > 5) {
      return sorted.take(_maxRecommendations).toList();
    }

    final maxRadiusKm = (nearestKm * 2.5) > (nearestKm + 1)
        ? nearestKm * 2.5
        : nearestKm + 1;
    return sorted
        .where((shop) => shop.distanceKm <= maxRadiusKm)
        .take(_maxRecommendations)
        .toList();
  }

  void _fitInitialView() {
    final position = _position;
    if (!_mapReady || position == null) return;

    final coordinates = <LatLng>[
      LatLng(position.latitude, position.longitude),
      ..._shops.map((shop) => LatLng(shop.lat, shop.lng)),
    ];
    if (coordinates.length == 1) {
      _mapController.move(coordinates.first, _followZoom);
      return;
    }

    _mapController.fitCamera(
      CameraFit.coordinates(
        coordinates: coordinates,
        padding: const EdgeInsets.all(44),
        maxZoom: 16,
      ),
    );
  }

  void _moveToUser() {
    final position = _position;
    if (!_mapReady || position == null) return;
    final zoom = _mapController.camera.zoom < _followZoom
        ? _followZoom
        : _mapController.camera.zoom;
    _mapController.move(LatLng(position.latitude, position.longitude), zoom);
  }

  void _toggleFollow() {
    setState(() => _followUser = !_followUser);
    if (_followUser) _moveToUser();
  }

  void _focusShop(BarberShopDto shop) {
    setState(() {
      _selectedShopId = shop.id;
      _followUser = false;
    });
    _mapController.move(LatLng(shop.lat, shop.lng), 16);
  }

  Future<void> _openDirections(BarberShopDto shop) async {
    final uri = Uri.parse(
      'https://www.google.com/maps/dir/?api=1&destination=${shop.lat},${shop.lng}',
    );
    final opened = await launchUrl(uri, mode: LaunchMode.externalApplication);
    if (!opened && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Google Maps tidak dapat dibuka.')),
      );
    }
  }

  Future<void> _openLocationSettings() async {
    if (_locationAccessStatus == LocationAccessStatus.serviceDisabled) {
      await _mapRepo.openLocationSettings();
    } else {
      await _mapRepo.openAppSettings();
    }
  }

  void _onBottomNavTap(int index) {
    switch (index) {
      case 0:
        context.go('/home');
        break;
      case 1:
        context.go('/catalog');
        break;
      case 2:
        context.go('/ai');
        break;
      case 3:
        context.go('/map');
        break;
      case 4:
        context.go('/profile');
        break;
    }
  }

  LatLng get _initialCenter {
    final position = _position;
    return position == null
        ? const LatLng(-6.2088, 106.8456)
        : LatLng(position.latitude, position.longitude);
  }

  Color get _statusColor {
    return switch (_statusType) {
      'error' => Colors.red.shade300,
      'success' => Colors.green.shade200,
      'loading' => Colors.lightBlue.shade300,
      _ => Colors.white.withValues(alpha: 0.65),
    };
  }

  @override
  Widget build(BuildContext context) {
    return Theme(
      data: AppTheme.dark(),
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Barbershop Terdekat'),
          backgroundColor: const Color(0xFF0A0A0A),
          foregroundColor: Colors.white,
          elevation: 0,
          scrolledUnderElevation: 0,
        ),
        body: SafeArea(
          child: Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 12, 20, 12),
                child: Row(
                  children: [
                    if (_statusType == 'success') ...[
                      Container(
                        width: 8,
                        height: 8,
                        decoration: const BoxDecoration(
                          color: Colors.greenAccent,
                          shape: BoxShape.circle,
                        ),
                      ),
                      const SizedBox(width: 8),
                    ],
                    Expanded(
                      child: Text(
                        _statusText,
                        style: TextStyle(fontSize: 13, color: _statusColor),
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                child: _locationDenied
                    ? _buildDeniedView()
                    : _buildMapContent(),
              ),
            ],
          ),
        ),
        bottomNavigationBar: MainBottomNav(
          currentIndex: 3,
          onTap: _onBottomNavTap,
        ),
      ),
    );
  }

  Widget _buildDeniedView() {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            color: Colors.amber.withValues(alpha: 0.1),
            border: Border.all(color: Colors.amber.withValues(alpha: 0.25)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Izin lokasi diperlukan',
                style: TextStyle(
                  color: Colors.amber.shade200,
                  fontWeight: FontWeight.w600,
                  fontSize: 15,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                _statusText,
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: Colors.amber.shade100.withValues(alpha: 0.8),
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 16),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _startTracking,
                  child: const Text('Izinkan lokasi & coba lagi'),
                ),
              ),
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: _openLocationSettings,
                  child: const Text('Buka pengaturan lokasi'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMapContent() {
    return Column(
      children: [
        Expanded(
          flex: 3,
          child: Stack(
            children: [
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(
                    color: Colors.white.withValues(alpha: 0.1),
                  ),
                ),
                clipBehavior: Clip.antiAlias,
                child: FlutterMap(
                  mapController: _mapController,
                  options: MapOptions(
                    initialCenter: _initialCenter,
                    initialZoom: 14,
                    maxZoom: 19,
                    onMapReady: () {
                      _mapReady = true;
                      if (_position != null) _fitInitialView();
                    },
                    onPositionChanged: (_, hasGesture) {
                      if (hasGesture && _followUser) {
                        setState(() => _followUser = false);
                      }
                    },
                    onTap: (_, _) => setState(() => _selectedShopId = null),
                  ),
                  children: [
                    TileLayer(
                      urlTemplate:
                          'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                      userAgentPackageName: 'com.example.barberselect_mobile',
                    ),
                    if (_position != null) CircleLayer(circles: _userCircles),
                    MarkerLayer(markers: _shopMarkers),
                    RichAttributionWidget(
                      attributions: [
                        TextSourceAttribution(
                          'OpenStreetMap contributors',
                          onTap: () => launchUrl(
                            Uri.parse(
                              'https://www.openstreetmap.org/copyright',
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              Positioned(
                top: 12,
                right: 24,
                child: FilledButton.icon(
                  onPressed: _toggleFollow,
                  icon: Icon(
                    _followUser ? Icons.gps_fixed : Icons.gps_not_fixed,
                    size: 16,
                  ),
                  label: Text(_followUser ? 'Mengikuti' : 'Ikuti lokasi'),
                  style: FilledButton.styleFrom(
                    backgroundColor: _followUser
                        ? Colors.lightBlue.shade600
                        : const Color(0xFF222222),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                    textStyle: const TextStyle(fontSize: 11),
                  ),
                ),
              ),
              if (_isLoading) _buildLoadingOverlay(),
            ],
          ),
        ),
        const SizedBox(height: 12),
        Expanded(flex: 2, child: _buildShopList()),
      ],
    );
  }

  List<CircleMarker> get _userCircles {
    final position = _position!;
    final point = LatLng(position.latitude, position.longitude);
    return [
      CircleMarker(
        point: point,
        radius: _searchRadiusMeters.toDouble(),
        useRadiusInMeter: true,
        color: Colors.lightBlue.withValues(alpha: 0.04),
        borderColor: Colors.lightBlue.withValues(alpha: 0.8),
        borderStrokeWidth: 1,
      ),
      if (position.accuracy < 800)
        CircleMarker(
          point: point,
          radius: position.accuracy,
          useRadiusInMeter: true,
          color: Colors.blue.withValues(alpha: 0.08),
          borderColor: Colors.blue.withValues(alpha: 0.7),
          borderStrokeWidth: 1,
        ),
      CircleMarker(
        point: point,
        radius: 16,
        color: Colors.blue.withValues(alpha: 0.2),
        borderColor: Colors.blue,
        borderStrokeWidth: 1,
      ),
      CircleMarker(
        point: point,
        radius: 8,
        color: Colors.blue,
        borderColor: Colors.white,
        borderStrokeWidth: 3,
      ),
    ];
  }

  List<Marker> get _shopMarkers {
    return _shops.map((shop) {
      final isSelected = _selectedShopId == shop.id;
      return Marker(
        point: LatLng(shop.lat, shop.lng),
        width: isSelected ? 150 : 125,
        height: 64,
        child: GestureDetector(
          onTap: () => _focusShop(shop),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: isSelected ? Colors.blue : const Color(0xFF1A1A2E),
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(
                    color: Colors.white.withValues(alpha: 0.3),
                  ),
                ),
                child: Text(
                  shop.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: isSelected ? 12 : 10,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const Icon(Icons.location_on, color: Colors.red, size: 28),
            ],
          ),
        ),
      );
    }).toList();
  }

  Widget _buildLoadingOverlay() {
    return Positioned.fill(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 12),
        decoration: BoxDecoration(
          color: const Color(0xFF0A0A0A).withValues(alpha: 0.72),
          borderRadius: BorderRadius.circular(16),
        ),
        child: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              CircularProgressIndicator(
                strokeWidth: 2,
                color: Colors.lightBlue.shade400,
              ),
              const SizedBox(height: 12),
              Text(
                _statusText,
                style: const TextStyle(color: Colors.white70, fontSize: 13),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildShopList() {
    if (_shops.isEmpty && !_isLoading) {
      return Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        child: Text(
          'Tidak ada barbershop dalam radius 5 km. Coba pindah lokasi atau perbesar area pencarian.',
          style: TextStyle(
            color: Colors.white.withValues(alpha: 0.6),
            fontSize: 13,
          ),
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      itemCount: _shops.length,
      itemBuilder: (context, index) => _buildShopCard(_shops[index], index),
    );
  }

  Widget _buildShopCard(BarberShopDto shop, int index) {
    final isSelected = _selectedShopId == shop.id;
    final isNearest = index == 0;
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () => _focusShop(shop),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: Colors.white.withValues(alpha: isSelected ? 0.3 : 0.1),
            ),
            color: Colors.white.withValues(alpha: isSelected ? 0.1 : 0.03),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      shop.name,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w600,
                        fontSize: 14,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    isNearest ? 'Terdekat' : '#${index + 1}',
                    style: TextStyle(
                      color: isNearest
                          ? Colors.green.shade200
                          : Colors.white.withValues(alpha: 0.7),
                      fontWeight: FontWeight.w600,
                      fontSize: 10,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Wrap(
                spacing: 12,
                runSpacing: 4,
                children: [
                  Text(
                    shop.distanceLabel,
                    style: TextStyle(
                      color: Colors.lightBlue.shade300,
                      fontWeight: FontWeight.w600,
                      fontSize: 12,
                    ),
                  ),
                  if (shop.ratingLabel != null)
                    Text(
                      shop.ratingLabel!,
                      style: TextStyle(
                        color: Colors.amber.shade300,
                        fontSize: 12,
                      ),
                    ),
                  Text(
                    shop.source == 'partner' ? 'Partner' : 'OSM',
                    style: TextStyle(
                      color: shop.source == 'partner'
                          ? Colors.green.shade300
                          : Colors.white.withValues(alpha: 0.4),
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
              if (shop.address != null) ...[
                const SizedBox(height: 6),
                Text(
                  shop.address!,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.6),
                    fontSize: 12,
                  ),
                ),
              ],
              if (shop.hours != null) ...[
                const SizedBox(height: 4),
                Text(
                  'Jam buka: ${shop.hours}',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.45),
                    fontSize: 11,
                  ),
                ),
              ],
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                child: OutlinedButton.icon(
                  onPressed: () => _openDirections(shop),
                  icon: const Icon(Icons.directions, size: 16),
                  label: const Text('Buka Google Maps'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _positionSubscription?.cancel();
    _mapController.dispose();
    super.dispose();
  }
}
