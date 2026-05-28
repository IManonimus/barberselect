import 'package:dio/dio.dart';

import '../../../core/network/api_client.dart';
import 'models/ai_recommend_response_dto.dart';

class AiRepository {
  final ApiClient apiClient;

  AiRepository({required this.apiClient});

  Future<AiRecommendResponseDto> recommend({
    required String query,
  }) async {
    final Response<dynamic> res = await apiClient.dio.post(
      '/api/ai/recommend',
      data: {
        'query': query,
      },
    );

    final data = res.data as Map<String, dynamic>;
    return AiRecommendResponseDto.fromJson(data);
  }
}

