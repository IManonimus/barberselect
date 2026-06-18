import 'package:flutter_bloc/flutter_bloc.dart';

import '../../data/catalog_repository.dart';
import '../../data/models/catalog_dto.dart';
import '../../data/models/category_dto.dart';

part 'catalog_state.dart';

class CatalogCubit extends Cubit<CatalogState> {
  final CatalogRepository repository;
  List<CategoryDto> _categories = const [];

  CatalogCubit({required this.repository}) : super(CatalogInitial());

  Future<void> loadInitial() async {
    if (isClosed) return;
    emit(CatalogLoading());
    try {
      final categories = await repository.fetchCategories();
      _categories = categories;
      final catalogs = await repository.fetchCatalogs(take: 50);

      if (catalogs.isEmpty) {
        emit(CatalogEmpty(categories: _categories));
        return;
      }

      emit(CatalogLoaded(categories: categories, catalogs: catalogs));
    } catch (e) {
      emit(CatalogError(e.toString()));
    }
  }

  Future<void> search({
    int? categoryId,
    required String q,
  }) async {
    if (isClosed) return;
    emit(CatalogLoading());
    try {
      final catalogs = await repository.fetchCatalogs(
        categoryId: categoryId,
        q: q,
        take: 50,
      );
      if (catalogs.isEmpty) {
        emit(CatalogEmpty(categories: _categories));
        return;
      }
      emit(CatalogLoaded(categories: _categories, catalogs: catalogs));
    } catch (e) {
      emit(CatalogError(e.toString()));
    }
  }
}

