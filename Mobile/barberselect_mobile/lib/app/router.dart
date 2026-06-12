import 'package:go_router/go_router.dart';

import '../features/admin/presentation/pages/admin_dashboard_page.dart';
import '../features/auth/presentation/pages/login_page.dart';
import '../features/auth/presentation/pages/register_page.dart';
import '../features/auth/presentation/pages/splash_screen.dart';
import '../features/catalog/presentation/pages/catalog_page.dart';
import '../features/dashboard/presentation/pages/home_page.dart';
import '../features/ai/presentation/pages/ai_recommend_page.dart';
import '../features/map/presentation/pages/nearby_map_page.dart';
import '../features/profile/presentation/pages/profile_page.dart';

class BarberSelectRouter {
  static GoRouter get router => GoRouter(
        initialLocation: '/',
        debugLogDiagnostics: false,
        routes: <GoRoute>[
          GoRoute(
            path: '/',
            builder: (context, state) => const SplashScreen(),
          ),
          GoRoute(
            path: '/login',
            builder: (context, state) => const LoginPage(),
          ),
          GoRoute(
            path: '/register',
            builder: (context, state) => const RegisterPage(),
          ),
          GoRoute(
            path: '/home',
            builder: (context, state) => const HomePage(),
          ),
          GoRoute(
            path: '/catalog',
            builder: (context, state) => const CatalogPage(),
          ),
          GoRoute(
            path: '/ai',
            builder: (context, state) => const AiRecommendPage(),
          ),
          GoRoute(
            path: '/map',
            builder: (context, state) => const NearbyMapPage(),
          ),
          GoRoute(
            path: '/profile',
            builder: (context, state) => const ProfilePage(),
          ),
          GoRoute(
            path: '/admin',
            builder: (context, state) => const AdminDashboardPage(),
          ),
        ],
      );
}