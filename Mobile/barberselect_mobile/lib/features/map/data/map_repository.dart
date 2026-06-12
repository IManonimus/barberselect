import 'package:dio/dio.dart';
import 'package:geolocator/geolocator.dart';

import '../../../core/network/api_client.dart';
import 'barber_shop_dto.dart';

enum LocationAccessStatus { granted, serviceDisabled, denied, deniedForever }

class MapRepository {
  final ApiClient apiClient;

  MapRepository({required this.apiClient});

  Future<List<BarberShopDto>> fetchNearbyBarbers({
    required double lat,
    required double lng,
    int radiusMeters = 5000,
  }) async {
    final Response res = await apiClient.dio.get(
      '/api/nearby-barbers',
      queryParameters: {
        'lat': lat.toString(),
        'lng': lng.toString(),
        'radius': radiusMeters.toString(),
      },
    );

    final data = res.data;
    final List rawItems = (data is Map ? (data['data'] as List? ?? []) : []);
    return rawItems
        .map((e) => BarberShopDto.fromJson((e as Map).cast<String, dynamic>()))
        .toList();
  }

  Future<LocationAccessStatus> ensureLocationAccess() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) return LocationAccessStatus.serviceDisabled;

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        return LocationAccessStatus.denied;
      }
    }
    if (permission == LocationPermission.deniedForever) {
      return LocationAccessStatus.deniedForever;
    }

    return LocationAccessStatus.granted;
  }

  Stream<Position> watchCurrentLocation() {
    return Geolocator.getPositionStream(
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.high,
        distanceFilter: 5,
      ),
    );
  }

  Future<bool> openAppSettings() => Geolocator.openAppSettings();

  Future<bool> openLocationSettings() => Geolocator.openLocationSettings();
}
