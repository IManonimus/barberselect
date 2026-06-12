class BarberShopDto {
  final String id;
  final String name;
  final String? address;
  final String? hours;
  final String? phone;
  final double? rating;
  final double lat;
  final double lng;
  final double distanceKm;
  final String source;

  const BarberShopDto({
    required this.id,
    required this.name,
    required this.lat,
    required this.lng,
    required this.distanceKm,
    required this.source,
    this.address,
    this.hours,
    this.phone,
    this.rating,
  });

  factory BarberShopDto.fromJson(Map<String, dynamic> json) {
    return BarberShopDto(
      id: (json['id'] ?? '') as String,
      name: (json['name'] ?? 'Barbershop') as String,
      address: json['address'] as String?,
      hours: json['hours'] as String?,
      phone: json['phone'] as String?,
      rating: (json['rating'] as num?)?.toDouble(),
      lat: (json['lat'] as num).toDouble(),
      lng: (json['lng'] as num).toDouble(),
      distanceKm: (json['distance_km'] as num?)?.toDouble() ?? 0,
      source: (json['source'] ?? 'osm') as String,
    );
  }

  String get distanceLabel {
    if (distanceKm.isNaN) return '';
    if (distanceKm < 1) return '${distanceKm.toStringAsFixed(2)} km';
    if (distanceKm < 10) return '${distanceKm.toStringAsFixed(2)} km';
    return '${distanceKm.toStringAsFixed(1)} km';
  }

  String? get ratingLabel {
    final value = rating;
    if (value == null || value.isNaN) return null;
    return '★ ${value.toStringAsFixed(1)}';
  }
}