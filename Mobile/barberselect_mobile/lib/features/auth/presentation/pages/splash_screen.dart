import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../data/auth_repository.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/network/api_client.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _bootstrap();
  }

  Future<void> _bootstrap() async {
    final authRepo = GetIt.instance<AuthRepository>();
    final token = await authRepo.readToken();

    if (!mounted) return;
    if (token != null && token.isNotEmpty) {
      GetIt.instance<ApiClient>().setAuthToken(token);
      context.go('/home');
    } else {
      context.go('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = AppTheme.dark();
    return MaterialApp(
      theme: theme,
      debugShowCheckedModeBanner: false,
      home: Scaffold(
        body: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Color(0xFF060713),
                Color(0xFF0E1021),
              ],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
          alignment: Alignment.center,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: const [
              SizedBox(
                height: 110,
                width: 110,
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: Color(0xFFFF4D6D),
                  ),
                ),
              ),
              SizedBox(height: 18),
              Text(
                'BarberSelect',
                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.w700,
                  letterSpacing: 0.2,
                  color: Colors.white,
                ),
              ),
              SizedBox(height: 10),
              Text(
                'Loading...',
                style: TextStyle(
                  color: Color(0xFFB7B9D6),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

