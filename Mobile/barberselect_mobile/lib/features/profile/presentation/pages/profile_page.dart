import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/main_bottom_nav.dart';
import '../../../auth/data/auth_repository.dart';
import '../../../../core/security/auth_local_datasource.dart';
import '../../data/profile_repository.dart';

class ProfilePage extends StatefulWidget {
  const ProfilePage({super.key});

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  bool _loading = true;
  String? _name;
  String? _email;
  bool _isAdmin = false;
  String? _error;
  bool _saving = false;

  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final profileRepo = GetIt.instance<ProfileRepository>();
      final user = await profileRepo.fetchProfile();
      if (!mounted) return;
      setState(() {
        _name = user.name;
        _email = user.email;
        _isAdmin = user.isAdmin;
        _nameController.text = _name ?? '';
        _emailController.text = _email ?? '';
        _loading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _loading = false;
      });
    }
  }

  Future<void> _logout() async {
    final authRepo = GetIt.instance<AuthRepository>();
    await authRepo.logout();
    if (!mounted) return;
    context.go('/login');
  }

  @override
  Widget build(BuildContext context) {
    final theme = AppTheme.dark();
    return Theme(
      data: theme,
      child: Scaffold(
        body: SafeArea(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'PROFIL',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.6),
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 3.5,
                        ),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        'Ubah profil',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 24,
                          fontWeight: FontWeight.w600,
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Perbarui nama, email, dan (opsional) password akun kamu.',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.65),
                          fontSize: 14,
                          height: 1.5,
                        ),
                      ),
                      const SizedBox(height: 20),
                      if (_error != null)
                        Container(
                          padding: const EdgeInsets.all(16),
                          margin: const EdgeInsets.only(bottom: 16),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(16),
                            color: const Color(
                              0x1AFF0000,
                            ).withValues(alpha: 0.1),
                            border: Border.all(color: const Color(0x33FF0000)),
                          ),
                          child: Text(
                            _error!,
                            style: const TextStyle(
                              color: Color(0xFFFF6B6B),
                              fontSize: 13,
                            ),
                          ),
                        ),
                      Expanded(
                        child: SingleChildScrollView(
                          child: Column(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(20),
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(24),
                                  color: const Color(0x08FFFFFF),
                                  border: Border.all(
                                    color: const Color(0x1AFFFFFF),
                                  ),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                'Nama',
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontWeight: FontWeight.w600,
                                                  fontSize: 14,
                                                ),
                                              ),
                                              const SizedBox(height: 8),
                                              TextField(
                                                controller: _nameController,
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 14,
                                                ),
                                                decoration: InputDecoration(
                                                  hintText: 'Nama lengkap',
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                'Email',
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontWeight: FontWeight.w600,
                                                  fontSize: 14,
                                                ),
                                              ),
                                              const SizedBox(height: 8),
                                              TextField(
                                                controller: _emailController,
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 14,
                                                ),
                                                decoration: InputDecoration(
                                                  hintText: 'Email address',
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 16),
                                    Row(
                                      children: [
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                'Password baru',
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontWeight: FontWeight.w600,
                                                  fontSize: 14,
                                                ),
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                '(opsional)',
                                                style: TextStyle(
                                                  color: Colors.white
                                                      .withValues(alpha: 0.4),
                                                  fontSize: 12,
                                                ),
                                              ),
                                              const SizedBox(height: 8),
                                              TextField(
                                                controller: _passwordController,
                                                obscureText: true,
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 14,
                                                ),
                                                decoration: InputDecoration(
                                                  hintText:
                                                      'Biarkan kosong jika tidak ingin mengganti',
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                'Konfirmasi password',
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontWeight: FontWeight.w600,
                                                  fontSize: 14,
                                                ),
                                              ),
                                              const SizedBox(height: 8),
                                              TextField(
                                                obscureText: true,
                                                style: const TextStyle(
                                                  color: Colors.white,
                                                  fontSize: 14,
                                                ),
                                                decoration: InputDecoration(
                                                  hintText:
                                                      'Ulangi password baru',
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 20),
                                    SizedBox(
                                      width: double.infinity,
                                      height: 48,
                                      child: ElevatedButton(
                                        onPressed: _saving
                                            ? null
                                            : () async {
                                                final messenger =
                                                    ScaffoldMessenger.of(
                                                      context,
                                                    );
                                                final repo =
                                                    GetIt.instance<
                                                      ProfileRepository
                                                    >();
                                                final local =
                                                    GetIt.instance<
                                                      AuthLocalDatasource
                                                    >();

                                                final name = _nameController
                                                    .text
                                                    .trim();
                                                final email = _emailController
                                                    .text
                                                    .trim();
                                                final passText =
                                                    _passwordController.text
                                                        .trim();

                                                if (name.isEmpty ||
                                                    email.isEmpty) {
                                                  messenger.showSnackBar(
                                                    const SnackBar(
                                                      content: Text(
                                                        'Nama dan email tidak boleh kosong.',
                                                      ),
                                                    ),
                                                  );
                                                  return;
                                                }

                                                setState(() => _saving = true);
                                                try {
                                                  final updated = await repo
                                                      .updateProfile(
                                                        name: name,
                                                        email: email,
                                                        password:
                                                            passText.isEmpty
                                                            ? null
                                                            : passText,
                                                      );
                                                  await local.saveUser(updated);

                                                  if (!mounted) return;
                                                  setState(() {
                                                    _name = updated.name;
                                                    _email = updated.email;
                                                    _passwordController.clear();
                                                    _error = null;
                                                    _saving = false;
                                                  });
                                                  messenger.showSnackBar(
                                                    const SnackBar(
                                                      content: Text(
                                                        'Profil berhasil diperbarui.',
                                                      ),
                                                      backgroundColor: Color(
                                                        0xFF10B981,
                                                      ),
                                                    ),
                                                  );
                                                } catch (e) {
                                                  if (!mounted) return;
                                                  setState(() {
                                                    _error = e.toString();
                                                    _saving = false;
                                                  });
                                                }
                                              },
                                        child: _saving
                                            ? const SizedBox(
                                                height: 20,
                                                width: 20,
                                                child:
                                                    CircularProgressIndicator(
                                                      strokeWidth: 2,
                                                    ),
                                              )
                                            : const Text('Simpan perubahan'),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(height: 16),
                              SizedBox(
                                width: double.infinity,
                                height: 48,
                                child: OutlinedButton(
                                  onPressed: _logout,
                                  child: const Text('Logout'),
                                ),
                              ),
                              if (_isAdmin) ...[
                                const SizedBox(height: 16),
                                OutlinedButton.icon(
                                  onPressed: () => context.go('/admin'),
                                  icon: const Icon(
                                    Icons.admin_panel_settings_outlined,
                                    size: 18,
                                  ),
                                  label: const Text('Admin Dashboard'),
                                ),
                              ],
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
          ),
        ),
        bottomNavigationBar: MainBottomNav(
          currentIndex: 4,
          onTap: (i) {
            switch (i) {
              case 0:
                context.go('/home');
                break;
              case 1:
                context.go('/catalog');
                break;
              case 2:
                context.go('/ai');
                break;
              case 3:
                context.go('/map');
                break;
              case 4:
                context.go('/profile');
                break;
            }
          },
        ),
      ),
    );
  }
}
