import 'package:flutter/material.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/main_bottom_nav.dart';
import '../../../landing/data/landing_repository.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  int _index = 0;
  late final Future<LandingPageDto> _landingFuture;

  @override
  void initState() {
    super.initState();
    _landingFuture = GetIt.instance<LandingRepository>().fetchLanding();
  }

  void _onTap(int idx) {
    setState(() => _index = idx);
    switch (idx) {
      case 0:
        context.go('/home');
        return;
      case 1:
        context.go('/catalog');
        return;
      case 2:
        context.go('/ai');
        return;
      case 3:
        context.go('/profile');
        return;
    }
  }

  @override
  Widget build(BuildContext context) {
    final theme = AppTheme.dark();
    return Theme(
      data: theme,
      child: Scaffold(
        body: SafeArea(
          child: FutureBuilder<LandingPageDto>(
            future: _landingFuture,
            builder: (context, snapshot) {
              final landing = snapshot.data;
              final trends = landing?.trends ?? const <TrendItemDto>[];

              if (snapshot.hasError) {
                return Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.cloud_off_rounded,
                            color: Colors.white.withValues(alpha: 0.3), size: 48),
                        const SizedBox(height: 16),
                        Text(
                          'Gagal memuat data home',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: 0.7),
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          '${snapshot.error}',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: 0.4),
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }

              if (!snapshot.hasData) {
                return const Center(
                  child: SizedBox(
                    width: 28,
                    height: 28,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                );
              }

              return ListView(
                padding: EdgeInsets.zero,
                children: [
                  _HeroSection(
                    title: landing!.heroTitle,
                    subtitle: landing.heroSubtitle,
                  ),
                  const SizedBox(height: 12),
                  // Feature cards
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Row(
                      children: [
                        Expanded(
                          child: _FeatureCard(
                            icon: Icons.auto_awesome_rounded,
                            label: 'Kurasi',
                            description: 'Koleksi gaya modern & klasik',
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _FeatureCard(
                            icon: Icons.person_rounded,
                            label: 'Personal',
                            description: 'Saran sesuai profil kamu',
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _FeatureCard(
                      icon: Icons.check_circle_rounded,
                      label: 'Praktis',
                      description: 'Mudah dijelaskan ke barber',
                    ),
                  ),
                  const SizedBox(height: 28),

                  // Catalog section
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _SectionKicker(text: 'CATALOG'),
                  ),
                  const SizedBox(height: 8),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'Katalog gaya rambut',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.w600,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'Temukan inspirasi gaya rambut dari berbagai kategori.',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.65),
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  // Catalog cards
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _CatalogPreviewGrid(),
                  ),
                  const SizedBox(height: 28),

                  // Trends section
                  if (trends.isNotEmpty) ...[
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      child: _SectionKicker(text: 'DISCOVER'),
                    ),
                    const SizedBox(height: 8),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 20),
                      child: Text(
                        'Tren rambut terbaru',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 22,
                          fontWeight: FontWeight.w600,
                          letterSpacing: -0.5,
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    SizedBox(
                      height: 160,
                      child: ListView.separated(
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.symmetric(horizontal: 20),
                        itemBuilder: (context, idx) {
                          final item = trends[idx];
                          return _TrendCard(title: item.title, desc: item.desc);
                        },
                        separatorBuilder: (_, _) => const SizedBox(width: 12),
                        itemCount: trends.length,
                      ),
                    ),
                    const SizedBox(height: 28),
                  ],

                  // AI section
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _SectionKicker(text: 'AI ASSISTANT'),
                  ),
                  const SizedBox(height: 8),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'Cari gaya rambutmu',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.w600,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'Deskripsikan kebutuhanmu dan dapatkan rekomendasi yang dipersonalisasi.',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.65),
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: InkWell(
                      borderRadius: BorderRadius.circular(24),
                      onTap: () => context.go('/ai'),
                      child: Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(24),
                          color: const Color(0x08FFFFFF),
                          border: Border.all(color: const Color(0x1AFFFFFF)),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 44,
                              height: 44,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(14),
                                color: Colors.white.withValues(alpha: 0.1),
                              ),
                              child: const Icon(
                                Icons.auto_awesome_rounded,
                                color: Colors.white,
                                size: 22,
                              ),
                            ),
                            const SizedBox(width: 14),
                            const Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Mulai rekomendasi AI',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w600,
                                      fontSize: 15,
                                    ),
                                  ),
                                  SizedBox(height: 4),
                                  Text(
                                    'Deskripsikan gaya rambut impianmu',
                                    style: TextStyle(
                                      color: Color(0xFFA3A3A3),
                                      fontSize: 12,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Icon(
                              Icons.chevron_right,
                              color: Colors.white.withValues(alpha: 0.3),
                              size: 20,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 28),

                  // About section
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: _SectionKicker(text: 'ABOUT'),
                  ),
                  const SizedBox(height: 8),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'Tentang BarberSelect',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.w600,
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Text(
                      'BarberSelect adalah platform referensi gaya rambut yang membantu kamu menemukan, menyimpan, dan membagikan inspirasi gaya rambut terbaik.',
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.65),
                        fontSize: 13,
                        height: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 20),
                    child: Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(24),
                        color: const Color(0x08FFFFFF),
                        border: Border.all(color: const Color(0x1AFFFFFF)),
                      ),
                      child: Column(
                        children: [
                          _AboutBullet(text: 'Koleksi gaya rambut modern & klasik yang dikurasi'),
                          const SizedBox(height: 12),
                          _AboutBullet(text: 'Rekomendasi AI yang dipersonalisasi sesuai profil kamu'),
                          const SizedBox(height: 12),
                          _AboutBullet(text: 'Mudah dijelaskan dan dikomunikasikan ke barber'),
                          const SizedBox(height: 12),
                          _AboutBullet(text: 'Temukan inspirasi dari tren terkini'),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),

                  // Footer
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
                    decoration: BoxDecoration(
                      border: Border(
                        top: BorderSide(color: Colors.white.withValues(alpha: 0.1)),
                      ),
                    ),
                    child: Text(
                      '© 2026 BarberSelect. All rights reserved.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.4),
                        fontSize: 11,
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                ],
              );
            },
          ),
        ),
        bottomNavigationBar: MainBottomNav(currentIndex: _index, onTap: _onTap),
      ),
    );
  }
}

class _HeroSection extends StatelessWidget {
  final String title;
  final String subtitle;

  const _HeroSection({required this.title, required this.subtitle});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            const Color(0xFF0A0A0A),
            const Color(0xFF0A0A0A).withValues(alpha: 0.95),
          ],
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Logo / Brand
          Row(
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.1),
                  border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
                ),
                child: Center(
                  child: Container(
                    width: 10,
                    height: 10,
                    decoration: const BoxDecoration(
                      shape: BoxShape.circle,
                      color: Color(0xCCFFFFFF),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Text(
                'BARBERSELECT',
                style: TextStyle(
                  color: Colors.white.withValues(alpha: 0.9),
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  letterSpacing: 3,
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),

          // Kicker
          Text(
            'TEMUKAN GAYA RAMBUT TERBAIKMU',
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.6),
              fontSize: 10,
              fontWeight: FontWeight.w600,
              letterSpacing: 3.5,
            ),
          ),
          const SizedBox(height: 12),

          // Title
          Text(
            title,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 28,
              fontWeight: FontWeight.w600,
              letterSpacing: -0.8,
              height: 1.15,
            ),
          ),
          const SizedBox(height: 12),

          // Subtitle
          Text(
            subtitle,
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.65),
              fontSize: 14,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 24),

          // CTA Buttons
          Row(
            children: [
              Expanded(
                child: SizedBox(
                  height: 48,
                  child: ElevatedButton(
                    onPressed: () => context.go('/ai'),
                    child: const Text('Mulai dengan AI'),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: SizedBox(
                  height: 48,
                  child: OutlinedButton(
                    onPressed: () => context.go('/catalog'),
                    child: const Text('Jelajahi katalog'),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _FeatureCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String description;

  const _FeatureCard({
    required this.icon,
    required this.label,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        color: const Color(0x08FFFFFF),
        border: Border.all(color: const Color(0x1AFFFFFF)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            label.toUpperCase(),
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.6),
              fontSize: 10,
              fontWeight: FontWeight.w600,
              letterSpacing: 1.5,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            description,
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.8),
              fontSize: 12,
              height: 1.4,
            ),
          ),
        ],
      ),
    );
  }
}

class _SectionKicker extends StatelessWidget {
  final String text;
  const _SectionKicker({required this.text});

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: TextStyle(
        color: Colors.white.withValues(alpha: 0.6),
        fontSize: 10,
        fontWeight: FontWeight.w600,
        letterSpacing: 3.5,
      ),
    );
  }
}

class _CatalogPreviewGrid extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final previewItems = [
      _CatalogPreviewData(
        name: 'Modern Side Part',
        category: 'Modern',
        description: 'Gaya rapi dengan belahan samping',
      ),
      _CatalogPreviewData(
        name: 'Textured Crop',
        category: 'Modern',
        description: 'Potongan pendek dengan tekstur',
      ),
      _CatalogPreviewData(
        name: 'Classic Pompadour',
        category: 'Klasik',
        description: 'Volume di atas, rapi di samping',
      ),
    ];

    return Column(
      children: [
        for (int i = 0; i < previewItems.length; i++) ...[
          _buildCard(context, previewItems[i]),
          if (i < previewItems.length - 1) const SizedBox(height: 12),
        ],
        const SizedBox(height: 12),
        SizedBox(
          width: double.infinity,
          height: 48,
          child: OutlinedButton.icon(
            onPressed: () => context.go('/catalog'),
            icon: const Icon(Icons.grid_view_rounded, size: 18),
            label: const Text('Lihat semua katalog'),
          ),
        ),
      ],
    );
  }

  Widget _buildCard(BuildContext context, _CatalogPreviewData item) {
    return InkWell(
      borderRadius: BorderRadius.circular(16),
      onTap: () => context.go('/catalog'),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          color: const Color(0x08FFFFFF),
          border: Border.all(color: const Color(0x1AFFFFFF)),
        ),
        child: Row(
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: const Color(0xFF1C1F33),
                borderRadius: const BorderRadius.horizontal(
                  left: Radius.circular(16),
                ),
              ),
              child: Icon(
                Icons.image_outlined,
                color: Colors.white.withValues(alpha: 0.2),
                size: 28,
              ),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            item.name,
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                              fontSize: 14,
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(999),
                            color: const Color(0x08FFFFFF),
                            border: Border.all(color: const Color(0x1AFFFFFF)),
                          ),
                          child: Text(
                            item.category,
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.7),
                              fontWeight: FontWeight.w600,
                              fontSize: 10,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      item.description,
                      style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.55),
                        fontSize: 12,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.only(right: 12),
              child: Icon(
                Icons.chevron_right,
                color: Colors.white.withValues(alpha: 0.3),
                size: 20,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _CatalogPreviewData {
  final String name;
  final String category;
  final String description;

  const _CatalogPreviewData({
    required this.name,
    required this.category,
    required this.description,
  });
}

class _TrendCard extends StatelessWidget {
  final String title;
  final String desc;

  const _TrendCard({required this.title, required this.desc});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 280,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        color: const Color(0x08FFFFFF),
        border: Border.all(color: const Color(0x1AFFFFFF)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'TREND',
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.55),
              fontSize: 10,
              fontWeight: FontWeight.w600,
              letterSpacing: 2.5,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            title,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w600,
              fontSize: 16,
            ),
          ),
          const SizedBox(height: 8),
          Expanded(
            child: Text(
              desc,
              style: TextStyle(
                color: Colors.white.withValues(alpha: 0.65),
                fontSize: 12,
                height: 1.5,
              ),
              maxLines: 3,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}

class _AboutBullet extends StatelessWidget {
  final String text;
  const _AboutBullet({required this.text});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white.withValues(alpha: 0.1),
            border: Border.all(color: Colors.white.withValues(alpha: 0.15)),
          ),
          child: Center(
            child: Text(
              '•',
              style: TextStyle(
                color: Colors.white.withValues(alpha: 0.7),
                fontSize: 10,
              ),
            ),
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Text(
            text,
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.7),
              fontSize: 13,
              height: 1.4,
            ),
          ),
        ),
      ],
    );
  }
}