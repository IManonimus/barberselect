class CatalogDto {
  final int id;
  final String name;
  final String? description;
  final String? careLevel;
  final String? faceShape;
  final String? hairType;
  final String? tips;
  final String? imageUrl;
  final String? categoryName;

  const CatalogDto({
    required this.id,
    required this.name,
    this.description,
    this.careLevel,
    this.faceShape,
    this.hairType,
    this.tips,
    this.imageUrl,
    this.categoryName,
  });

  factory CatalogDto.fromJson(Map<String, dynamic> json) {
    final category = json['category'];
    final categoryName = category is Map<String, dynamic>
        ? category['name'] as String?
        : (json['category_name'] as String?);

    return CatalogDto(
      id: (json['id'] as num).toInt(),
      name: (json['name'] ?? '') as String,
      description: json['description'] as String?,
      careLevel: json['care_level'] as String?,
      faceShape: json['face_shape'] as String?,
      hairType: json['hair_type'] as String?,
      tips: json['tips'] as String?,
      imageUrl: json['image_url'] as String?,
      categoryName: categoryName,
    );
  }
}

