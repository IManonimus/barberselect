import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../auth/data/auth_repository.dart';

class AdminDashboardPage extends StatefulWidget {
  const AdminDashboardPage({super.key});

  @override
  State<AdminDashboardPage> createState() => _AdminDashboardPageState();
}

class _AdminDashboardPageState extends State<AdminDashboardPage> {
  bool _loading = true;
  bool _isAdmin = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final user = await GetIt.instance<AuthRepository>().readUser();
      if (!mounted) return;
      setState(() {
        _isAdmin = user?.isAdmin ?? false;
        _loading = false;
      });
      if (!_isAdmin) {
        context.go('/home');
      }
    } catch (e) {
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = AppTheme.dark();
    return Theme(
      data: theme,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Admin'),
        ),
        body: SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : _error != null
                    ? Text(_error!, style: const TextStyle(color: Color(0xFFFF4D6D)))
                    : Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Manage BarberSelect (Mobile)',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w900,
                              fontSize: 16,
                            ),
                          ),
                          const SizedBox(height: 12),
                          const Text(
                            'UI admin ini siap untuk dihubungkan ke API Laravel (categories, catalogs, landing-page settings).',
                            style: TextStyle(color: Color(0xFFB7B9D6)),
                          ),
                          const SizedBox(height: 18),
                          _AdminActionTile(
                            icon: Icons.category_outlined,
                            title: 'Kelola Kategori',
                            subtitle: 'Tambah/edit/hapus kategori katalog',
                            onTap: () {},
                          ),
                          _AdminActionTile(
                            icon: Icons.style_outlined,
                            title: 'Kelola Katalog',
                            subtitle: 'Tambah/edit/hapus model rambut',
                            onTap: () {},
                          ),
                          _AdminActionTile(
                            icon: Icons.settings_outlined,
                            title: 'Landing Page (Mobile)',
                            subtitle: 'Hero, Tren, AI Assistant, About',
                            onTap: () {},
                          ),
                          const Spacer(),
                          SizedBox(
                            width: double.infinity,
                            height: 48,
                            child: FilledButton.tonal(
                              onPressed: () => context.go('/home'),
                              child: const Text('Kembali ke aplikasi'),
                            ),
                          )
                        ],
                      ),
          ),
        ),
      ),
    );
  }
}

class _AdminActionTile extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  const _AdminActionTile({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          color: const Color(0xFF0E1021).withValues(alpha: 0.9),
          border: Border.all(color: const Color(0x1FFFFFFF)),
        ),
        child: Row(
          children: [
            Container(
              width: 46,
              height: 46,
              decoration: BoxDecoration(
                color: const Color(0xFF7C5CFF).withValues(alpha: 0.18),
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: const Color(0x1FFFFFFF)),
              ),
              child: Icon(icon, color: const Color(0xFFFF4D6D)),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w900),
                  ),
                  const SizedBox(height: 4),
                  Text(subtitle, style: const TextStyle(color: Color(0xFFB7B9D6), fontSize: 12)),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: Colors.white70),
          ],
        ),
      ),
    );
  }
}

