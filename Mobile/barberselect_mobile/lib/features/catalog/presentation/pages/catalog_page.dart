import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:get_it/get_it.dart';
import 'package:go_router/go_router.dart';

import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/main_bottom_nav.dart';
import '../cubit/catalog_cubit.dart';
import '../../data/catalog_repository.dart';
import '../../data/models/catalog_dto.dart';
import '../../data/models/category_dto.dart';

class CatalogPage extends StatefulWidget {
  const CatalogPage({super.key});

  @override
  State<CatalogPage> createState() => _CatalogPageState();
}

class _CatalogPageState extends State<CatalogPage> {
  int _selectedCategory = 0;

  late final CatalogCubit _cubit;

  @override
  void initState() {
    super.initState();
    _cubit = CatalogCubit(repository: GetIt.instance<CatalogRepository>());
    _cubit.loadInitial();
  }

  @override
  void dispose() {
    _cubit.close();
    super.dispose();
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
            child: BlocProvider.value(
              value: _cubit,
              child: BlocBuilder<CatalogCubit, CatalogState>(
                builder: (context, state) {
                  final categories = state is CatalogLoaded
                      ? state.categories
                      : state is CatalogEmpty
                      ? state.categories
                      : <CategoryDto>[];

                  final catalogs = state is CatalogLoaded
                      ? state.catalogs
                      : <CatalogDto>[];

                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Kicker
                      Text(
                        'CATALOG',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.6),
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                          letterSpacing: 3.5,
                        ),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        'Katalog gaya rambut',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 24,
                          fontWeight: FontWeight.w600,
                          letterSpacing: -0.5,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Temukan inspirasi gaya rambut dari berbagai kategori.',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.65),
                          fontSize: 14,
                          height: 1.5,
                        ),
                      ),
                      const SizedBox(height: 20),
                      _CategoryChips(
                        selectedCategoryId: _selectedCategory,
                        categories: categories,
                        onSelectedCategoryId: (v) =>
                            setState(() => _selectedCategory = v),
                      ),
                      const SizedBox(height: 16),
                      Expanded(
                        child: Builder(
                          builder: (context) {
                            if (state is CatalogLoading ||
                                state is CatalogInitial) {
                              return const Center(
                                child: CircularProgressIndicator(),
                              );
                            }
                            if (state is CatalogError) {
                              return Center(
                                child: Text(
                                  state.message,
                                  style: const TextStyle(
                                    color: Color(0xFFFF6B6B),
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              );
                            }
                            if (state is CatalogEmpty) {
                              return const Center(
                                child: Text(
                                  'Tidak ada gaya yang cocok.',
                                  style: TextStyle(color: Color(0xFFA3A3A3)),
                                ),
                              );
                            }
                            return GridView.builder(
                              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                                crossAxisCount: 2,
                                crossAxisSpacing: 12,
                                mainAxisSpacing: 12,
                                childAspectRatio: 0.72,
                              ),
                              itemCount: catalogs.length,
                              itemBuilder: (context, index) {
                                return CatalogModelCard(
                                  catalog: catalogs[index],
                                );
                              },
                            );
                          },
                        ),
                      ),
                    ],
                  );
                },
              ),
            ),
          ),
        ),
        bottomNavigationBar: MainBottomNav(
          currentIndex: 1,
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
    );
  }
}

class _CategoryChips extends StatelessWidget {
  final int selectedCategoryId;
  final List<CategoryDto> categories;
  final ValueChanged<int> onSelectedCategoryId;

  const _CategoryChips({
    required this.selectedCategoryId,
    required this.categories,
    required this.onSelectedCategoryId,
  });

  @override
  Widget build(BuildContext context) {
    final chips = <({int id, String label})>[];
    chips.add((id: 0, label: 'Semua'));
    chips.addAll(categories.map((c) => (id: c.id, label: c.name)));
    return SizedBox(
      height: 40,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemCount: chips.length,
        separatorBuilder: (_, _) => const SizedBox(width: 8),
        itemBuilder: (context, i) {
          final chip = chips[i];
          final isSel = selectedCategoryId == chip.id;
          return InkWell(
            borderRadius: BorderRadius.circular(999),
            onTap: () => onSelectedCategoryId(chip.id),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(999),
                color: isSel
                    ? Colors.white
                    : const Color(0x08FFFFFF),
                border: isSel
                    ? null
                    : Border.all(color: const Color(0x1AFFFFFF)),
              ),
              child: Text(
                chip.label,
                style: TextStyle(
                  color: isSel ? const Color(0xFF0A0A0A) : Colors.white.withValues(alpha: 0.85),
                  fontWeight: FontWeight.w600,
                  fontSize: 12,
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class CatalogModelCard extends StatelessWidget {
  final CatalogDto catalog;

  const CatalogModelCard({super.key, required this.catalog});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: const Color(0x1AFFFFFF)),
        color: const Color(0x08FFFFFF),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(24),
        onTap: () {},
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: Stack(
                fit: StackFit.expand,
                children: [
                  if (catalog.imageUrl != null && catalog.imageUrl!.isNotEmpty)
                    ClipRRect(
                      borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
                      child: Image.network(catalog.imageUrl!, fit: BoxFit.cover),
                    )
                  else
                    Container(
                      decoration: const BoxDecoration(
                        color: Color(0xFF1C1F33),
                        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
                      ),
                    ),
                  Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Color(0x00000000), Color(0xB30A0A0A)],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                  ),
                  Positioned(
                    top: 12,
                    right: 12,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(999),
                        color: const Color(0x08FFFFFF),
                        border: Border.all(color: const Color(0x1AFFFFFF)),
                      ),
                      child: Text(
                        catalog.categoryName ?? 'Modern',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.8),
                          fontWeight: FontWeight.w600,
                          fontSize: 10,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    catalog.name,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w600,
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 6),
                  Text(
                    catalog.description ?? '',
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: 0.65),
                      fontSize: 12,
                      height: 1.4,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Text(
                        'Lihat detail',
                        style: TextStyle(
                          color: Colors.white.withValues(alpha: 0.85),
                          fontWeight: FontWeight.w600,
                          fontSize: 12,
                        ),
                      ),
                      const SizedBox(width: 4),
                      Icon(
                        Icons.arrow_forward,
                        size: 14,
                        color: Colors.white.withValues(alpha: 0.7),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}