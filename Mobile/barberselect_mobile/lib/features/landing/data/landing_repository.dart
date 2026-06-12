import 'package:dio/dio.dart';

import '../../../core/network/api_client.dart';

class TrendItemDto {
  final String title;
  final String desc;

  const TrendItemDto({required this.title, required this.desc});

  factory TrendItemDto.fromJson(Map<String, dynamic> json) {
    return TrendItemDto(
      title: (json['title'] ?? '') as String,
      desc: (json['desc'] ?? '') as String,
    );
  }
}

class MapConfigDto {
  final String kicker;
  final String title;
  final String subtitle;
  final String hint;

  const MapConfigDto({
    this.kicker = 'LOCATIONS',
    this.title = 'Temukan barbershop terdekat',
    this.subtitle = 'Map akan menemukan barbershop 5km dari sekitar anda',
    this.hint = 'Peta mengikuti lokasi kamu. Rekomendasi diperbarui otomatis saat kamu bergerak.',
  });

  factory MapConfigDto.fromJson(Map<String, dynamic> json) {
    return MapConfigDto(
      kicker: (json['kicker'] ?? 'LOCATIONS') as String,
      title: (json['title'] ?? 'Temukan barbershop terdekat') as String,
      subtitle: (json['subtitle'] ?? '') as String,
      hint: (json['hint'] ?? '') as String,
    );
  }
}

class LandingPageDto {
  final String heroTitle;
  final String heroSubtitle;
  final List<TrendItemDto> trends;
  final MapConfigDto mapConfig;

  const LandingPageDto({
    required this.heroTitle,
    required this.heroSubtitle,
    required this.trends,
    required this.mapConfig,
  });

  factory LandingPageDto.fromJson(Map<String, dynamic> json) {
    final hero = (json['hero'] is Map) ? (json['hero'] as Map).cast<String, dynamic>() : const <String, dynamic>{};
    final trends = (json['trends'] is Map) ? (json['trends'] as Map).cast<String, dynamic>() : const <String, dynamic>{};
    final itemsRaw = trends['items'];
    final items = (itemsRaw is List ? itemsRaw : const <dynamic>[])
        .map((e) => TrendItemDto.fromJson((e as Map).cast<String, dynamic>()))
        .toList();
    final mapRaw = (json['map'] is Map) ? (json['map'] as Map).cast<String, dynamic>() : const <String, dynamic>{};

    return LandingPageDto(
      heroTitle: (hero['title'] ?? 'BarberSelect') as String,
      heroSubtitle: (hero['subtitle'] ?? '') as String,
      trends: items,
      mapConfig: MapConfigDto.fromJson(mapRaw),
    );
  }
}

class LandingRepository {
  final ApiClient apiClient;
  LandingRepository({required this.apiClient});

  Future<LandingPageDto> fetchLanding() async {
    final Response res = await apiClient.dio.get('/api/landing-page');
    final data = (res.data as Map).cast<String, dynamic>();
    final payload = (data['data'] ?? const <String, dynamic>{}) as Map<String, dynamic>;
    return LandingPageDto.fromJson(payload);
  }
}

