import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/main_bottom_nav.dart';
import '../cubit/ai_cubit.dart';
import '../../data/ai_repository.dart';

class AiRecommendPage extends StatefulWidget {
  const AiRecommendPage({super.key});

  @override
  State<AiRecommendPage> createState() => _AiRecommendPageState();
}

class _AiRecommendPageState extends State<AiRecommendPage> {
  String _faceShape = 'Oval';
  String _hairType = 'Lurus';
  String _activity = 'Kantoran';
  String _stylePref = 'Clean & modern';

  Future<void> _submit() async {
    final query =
        'wajah ${_faceShape.toLowerCase()}, aktivitas ${_activity.toLowerCase()}, gaya ${_stylePref.toLowerCase()}, jenis rambut ${_hairType.toLowerCase()}';
    context.read<AiCubit>().recommend(query);
  }

  @override
  Widget build(BuildContext context) {
    final theme = AppTheme.dark();
    return BlocProvider(
      create: (_) => AiCubit(repository: GetIt.instance<AiRepository>()),
      child: Theme(
        data: theme,
        child: Scaffold(
          body: SafeArea(
            child: ListView(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 16),
              children: [
                // Kicker
                Text(
                  'AI ASSISTANT',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.6),
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    letterSpacing: 3.5,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Cari gaya rambutmu',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.w600,
                    letterSpacing: -0.5,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Deskripsikan kebutuhanmu dan dapatkan rekomendasi yang dipersonalisasi.',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: 0.65),
                    fontSize: 14,
                    height: 1.5,
                  ),
                ),
                const SizedBox(height: 20),

                // Form section
                Container(
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
                        'Deskripsimu',
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w600,
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(24),
                          color: const Color(0xFF0A0A0A).withValues(alpha: 0.6),
                          border: Border.all(color: const Color(0x1AFFFFFF)),
                        ),
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _LabeledDropdown(
                              label: 'Bentuk wajah',
                              value: _faceShape,
                              items: const ['Oval', 'Bulat', 'Kotak'],
                              onChanged: (v) => setState(() => _faceShape = v),
                            ),
                            const SizedBox(height: 12),
                            _LabeledDropdown(
                              label: 'Jenis rambut (opsional)',
                              value: _hairType,
                              items: const ['Lurus', 'Bergelombang', 'Keriting'],
                              onChanged: (v) => setState(() => _hairType = v),
                            ),
                            const SizedBox(height: 12),
                            _LabeledDropdown(
                              label: 'Aktivitas harian',
                              value: _activity,
                              items: const ['Kantoran', 'Kuliah', 'Freelance', 'Olahraga'],
                              onChanged: (v) => setState(() => _activity = v),
                            ),
                            const SizedBox(height: 12),
                            _LabeledDropdown(
                              label: 'Preferensi style',
                              value: _stylePref,
                              items: const ['Clean & modern', 'Klasik & elegan', 'Bold & edgy'],
                              onChanged: (v) => setState(() => _stylePref = v),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),
                      BlocBuilder<AiCubit, AiState>(
                        builder: (context, state) {
                          final bool isLoading = state is AiLoading;
                          final String? message =
                              state is AiError ? state.message : null;

                          return Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              if (message != null)
                                Padding(
                                  padding: const EdgeInsets.only(bottom: 8),
                                  child: Text(
                                    message,
                                    style: const TextStyle(
                                      color: Color(0xFFFF6B6B),
                                      fontSize: 12,
                                    ),
                                  ),
                                ),
                              SizedBox(
                                width: double.infinity,
                                height: 48,
                                child: ElevatedButton(
                                  onPressed: isLoading ? null : _submit,
                                  child: isLoading
                                      ? const SizedBox(
                                          height: 20,
                                          width: 20,
                                          child: CircularProgressIndicator(strokeWidth: 2),
                                        )
                                      : const Text('Cari'),
                                ),
                              ),
                            ],
                          );
                        },
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 20),

                // Result section
                BlocBuilder<AiCubit, AiState>(
                  builder: (context, state) {
                    final response = state is AiSuccess ? state.response : null;

                    if (response == null) return const SizedBox.shrink();

                    return Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(24),
                        color: const Color(0x08FFFFFF),
                        border: Border.all(color: const Color(0x1AFFFFFF)),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Hasil rekomendasi',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                              fontSize: 14,
                            ),
                          ),
                          const SizedBox(height: 12),
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(16),
                              color: const Color(0xFF0A0A0A).withValues(alpha: 0.6),
                              border: Border.all(color: const Color(0x1AFFFFFF)),
                            ),
                            child: Text(
                              response.recommendation,
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.75),
                                fontSize: 13,
                                height: 1.5,
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),
                          const Text(
                            'Referensi dari katalog',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                              fontSize: 14,
                            ),
                          ),
                          const SizedBox(height: 12),
                          GridView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: 2,
                              crossAxisSpacing: 12,
                              mainAxisSpacing: 12,
                              childAspectRatio: 0.8,
                            ),
                            itemCount: response.catalogRecommendations.length,
                            itemBuilder: (context, index) {
                              final item = response.catalogRecommendations[index];
                              return Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(24),
                                  border: Border.all(color: const Color(0x1AFFFFFF)),
                                  color: const Color(0x08FFFFFF),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Expanded(
                                      child: Container(
                                        width: double.infinity,
                                        decoration: BoxDecoration(
                                          color: const Color(0xFF1C1F33),
                                          borderRadius: const BorderRadius.vertical(
                                            top: Radius.circular(24),
                                          ),
                                        ),
                                        child: const Icon(
                                          Icons.image_outlined,
                                          color: Color(0xFF3C3C3C),
                                          size: 40,
                                        ),
                                      ),
                                    ),
                                    Padding(
                                      padding: const EdgeInsets.all(12),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            item.name,
                                            style: const TextStyle(
                                              color: Colors.white,
                                              fontWeight: FontWeight.w600,
                                              fontSize: 12,
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            item.category ?? '—',
                                            style: TextStyle(
                                              color: Colors.white.withValues(alpha: 0.65),
                                              fontSize: 11,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            },
                          ),
                        ],
                      ),
                    );
                  },
                ),
                const SizedBox(height: 20),

                // Disclaimer
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(24),
                    color: const Color(0x1AFFB800).withValues(alpha: 0.1),
                    border: Border.all(color: const Color(0x33FFB800)),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Disclaimer',
                        style: TextStyle(
                          color: Color(0xFFFFB800),
                          fontWeight: FontWeight.w600,
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        'Rekomendasi AI dibuat berdasarkan deskripsi yang kamu berikan. Hasil mungkin tidak 100% akurat. Gunakan sebagai referensi dan konsultasikan dengan barber profesional.',
                        style: TextStyle(
                          color: const Color(0xFFFFB800).withValues(alpha: 0.8),
                          fontSize: 12,
                          height: 1.5,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
          bottomNavigationBar: MainBottomNav(
            currentIndex: 2,
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
                  context.go('/profile');
                  break;
              }
            },
          ),
        ),
      ),
    );
  }
}

class _LabeledDropdown extends StatelessWidget {
  final String label;
  final String value;
  final List<String> items;
  final ValueChanged<String> onChanged;

  const _LabeledDropdown({
    required this.label,
    required this.value,
    required this.items,
    required this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w800,
          ),
        ),
        const SizedBox(height: 8),
        DecoratedBox(
          decoration: BoxDecoration(
            color: const Color(0xFF0E1021).withValues(alpha: 0.85),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: const Color(0x1FFFFFFF)),
          ),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
            child: DropdownButtonHideUnderline(
              child: DropdownButton<String>(
                value: value,
                isExpanded: true,
                dropdownColor: const Color(0xFF0E1021),
                onChanged: (v) => onChanged(v ?? value),
                items: items
                    .map(
                      (it) => DropdownMenuItem<String>(
                        value: it,
                        child: Text(it, style: const TextStyle(color: Colors.white)),
                      ),
                    )
                    .toList(),
              ),
            ),
          ),
        ),
      ],
    );
  }
}