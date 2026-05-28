class CategoryDto {
  final int id;
  final String name;
  final String? imageUrl;

  const CategoryDto({
    required this.id,
    required this.name,
    this.imageUrl,
  });

  factory CategoryDto.fromJson(Map<String, dynamic> json) {
    return CategoryDto(
      id: (json['id'] as num).toInt(),
      name: (json['name'] ?? '') as String,
      imageUrl: json['image_url'] as String?,
    );
  }
}

