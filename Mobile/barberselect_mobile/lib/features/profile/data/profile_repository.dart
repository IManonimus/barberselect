import '../../../core/network/api_client.dart';
import '../../auth/data/models/user_dto.dart';

class ProfileRepository {
  final ApiClient apiClient;

  ProfileRepository({required this.apiClient});

  Future<UserDto> fetchProfile() async {
    final res = await apiClient.dio.get('/api/profile');
    final data = res.data as Map<String, dynamic>;
    return UserDto.fromJson((data['user'] ?? data).cast<String, dynamic>());
  }

  Future<UserDto> updateProfile({
    required String name,
    required String email,
    String? password,
  }) async {
    final payload = <String, dynamic>{
      'name': name,
      'email': email,
      if (password != null && password.isNotEmpty) 'password': password,
    };

    final res = await apiClient.dio.put('/api/profile', data: payload);
    final data = res.data as Map<String, dynamic>;
    return UserDto.fromJson((data['user'] ?? data).cast<String, dynamic>());
  }
}

