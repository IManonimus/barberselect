import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../core/theme/app_theme.dart';
import 'router.dart';

class BarberSelectApp extends StatelessWidget {
  const BarberSelectApp({super.key});

  @override
  Widget build(BuildContext context) {
    final GoRouter router = BarberSelectRouter.router;

    return MaterialApp.router(
      debugShowCheckedModeBanner: false,
      title: 'BarberSelect',
      theme: AppTheme.dark(),
      routerConfig: router,
    );
  }
}

