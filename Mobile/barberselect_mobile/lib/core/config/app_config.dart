class AppConfig {
  /// Base URL untuk REST API Laravel.
  ///
  /// Untuk emulator Android:
  /// - Android Emulator: `http://10.0.2.2:8000`
  /// - iOS Simulator: pastikan server bisa diakses dari simulator.
  static const String apiBaseUrl = String.fromEnvironment(
    'API_BASE_URL',
    defaultValue: 'http://10.0.2.2:8000',
  );
}

