import 'package:get_it/get_it.dart';

import '../core/config/app_config.dart';
import '../core/network/api_client.dart';
import '../core/security/auth_local_datasource.dart';
import '../features/ai/data/ai_repository.dart';
import '../features/catalog/data/catalog_repository.dart';
import '../features/profile/data/profile_repository.dart';
import '../features/landing/data/landing_repository.dart';
import '../features/auth/data/auth_repository.dart';

/// Central place to register dependencies for the app (GetIt).
class AppBootstrap {
  static final _sl = GetIt.instance;
  static bool _initialized = false;

  static Future<void> init() async {
    if (_initialized) return;
    _initialized = true;

    _sl
      ..registerLazySingleton<ApiClient>(
        () => ApiClient(baseUrl: AppConfig.apiBaseUrl),
      )
      ..registerLazySingleton<AuthLocalDatasource>(
        () => AuthLocalDatasource(),
      )
      ..registerLazySingleton<CatalogRepository>(
        () => CatalogRepository(apiClient: _sl<ApiClient>()),
      )
      ..registerLazySingleton<AiRepository>(
        () => AiRepository(apiClient: _sl<ApiClient>()),
      )
      ..registerLazySingleton<ProfileRepository>(
        () => ProfileRepository(apiClient: _sl<ApiClient>()),
      )
      ..registerLazySingleton<LandingRepository>(
        () => LandingRepository(apiClient: _sl<ApiClient>()),
      )
      ..registerLazySingleton<AuthRepository>(
        () => AuthRepository(
          apiClient: _sl<ApiClient>(),
          authLocalDatasource: _sl<AuthLocalDatasource>(),
        ),
      );
  }
}

