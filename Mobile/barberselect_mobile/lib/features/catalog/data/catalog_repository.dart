import '../../../core/network/api_client.dart';
import 'models/catalog_dto.dart';
import 'models/category_dto.dart';

class CatalogRepository {
  final ApiClient apiClient;

  CatalogRepository({required this.apiClient});

  Future<List<CategoryDto>> fetchCategories() async {
    final res = await apiClient.dio.get('/api/categories');
    final data = res.data;

    final list = _extractList(data);
    return list
        .map((e) => CategoryDto.fromJson((e as Map).cast<String, dynamic>()))
        .toList();
  }

  Future<List<CatalogDto>> fetchCatalogs({
    int? categoryId,
    String? q,
    int take = 50,
  }) async {
    final queryParameters = <String, dynamic>{
      'category_id': ?categoryId,
      if (q != null && q.trim().isNotEmpty) 'q': q.trim(),
      'take': take,
    };

    final res = await apiClient.dio.get('/api/catalogs', queryParameters: queryParameters);
    final data = res.data;

    final list = _extractList(data);
    return list
        .map((e) => CatalogDto.fromJson((e as Map).cast<String, dynamic>()))
        .toList();
  }

  List<dynamic> _extractList(dynamic data) {
    if (data is List) return data;
    if (data is Map) {
      final items = data['data'] ?? data['items'] ?? data['catalogs'];
      if (items is List) return items;
    }
    throw StateError('Unexpected response shape for list');
  }
}

