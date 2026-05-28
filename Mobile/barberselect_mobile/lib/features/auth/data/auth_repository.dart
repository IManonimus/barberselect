import 'package:dio/dio.dart';

import '../../../core/network/api_client.dart';
import '../../../core/security/auth_local_datasource.dart';
import './models/user_dto.dart';

class AuthSession {
  final String token;
  final UserDto user;

  const AuthSession({
    required this.token,
    required this.user,
  });
}

class AuthRepository {
  final ApiClient apiClient;
  final AuthLocalDatasource authLocalDatasource;

  AuthRepository({
    required this.apiClient,
    required this.authLocalDatasource,
  });

  Future<AuthSession> login({
    required String email,
    required String password,
  }) async {
    final Response<dynamic> res = await apiClient.dio.post(
      '/api/auth/login',
      data: {
        'email': email,
        'password': password,
      },
    );

    final data = res.data as Map<String, dynamic>;
    final token = (data['token'] ?? data['access_token']) as String;
    final user = UserDto.fromJson((data['user'] ?? const {}).cast<String, dynamic>());

    apiClient.setAuthToken(token);
    await authLocalDatasource.saveToken(token);
    await authLocalDatasource.saveUser(user);

    return AuthSession(token: token, user: user);
  }

  Future<AuthSession> register({
    required String name,
    required String email,
    required String password,
  }) async {
    final Response<dynamic> res = await apiClient.dio.post(
      '/api/auth/register',
      data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
      },
    );

    final data = res.data as Map<String, dynamic>;
    final token = (data['token'] ?? data['access_token']) as String;
    final user = UserDto.fromJson((data['user'] ?? const {}).cast<String, dynamic>());

    apiClient.setAuthToken(token);
    await authLocalDatasource.saveToken(token);
    await authLocalDatasource.saveUser(user);

    return AuthSession(token: token, user: user);
  }

  Future<void> logout() async {
    apiClient.clearAuthToken();
    await authLocalDatasource.clear();
  }

  Future<String?> readToken() {
    return authLocalDatasource.readToken();
  }

  Future<UserDto?> readUser() {
    return authLocalDatasource.readUser();
  }
}

