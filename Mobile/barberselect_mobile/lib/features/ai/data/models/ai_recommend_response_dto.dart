class CatalogRecommendationDto {
  final int id;
  final String name;
  final String? description;
  final String? category;
  final String? imageUrl;
  final String? detailUrl;

  const CatalogRecommendationDto({
    required this.id,
    required this.name,
    this.description,
    this.category,
    this.imageUrl,
    this.detailUrl,
  });

  factory CatalogRecommendationDto.fromJson(Map<String, dynamic> json) {
    return CatalogRecommendationDto(
      id: (json['id'] as num).toInt(),
      name: (json['name'] ?? '') as String,
      description: json['description'] as String?,
      category: json['category'] as String?,
      imageUrl: json['image_url'] as String?,
      detailUrl: json['detail_url'] as String?,
    );
  }
}

class AiRecommendResponseDto {
  final String recommendation;
  final List<CatalogRecommendationDto> catalogRecommendations;

  const AiRecommendResponseDto({
    required this.recommendation,
    required this.catalogRecommendations,
  });

  factory AiRecommendResponseDto.fromJson(Map<String, dynamic> json) {
    final list = json['catalog_recommendations'] ?? json['catalogRecommendations'];
    final items = (list is List ? list : <dynamic>[])
        .map((e) => CatalogRecommendationDto.fromJson((e as Map).cast<String, dynamic>()))
        .toList();

    return AiRecommendResponseDto(
      recommendation: (json['recommendation'] ?? '') as String,
      catalogRecommendations: items,
    );
  }
}

