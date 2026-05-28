part of 'catalog_cubit.dart';

abstract class CatalogState {
  const CatalogState();
}

class CatalogInitial extends CatalogState {
  const CatalogInitial();
}

class CatalogLoading extends CatalogState {
  const CatalogLoading();
}

class CatalogLoaded extends CatalogState {
  final List<CategoryDto> categories;
  final List<CatalogDto> catalogs;

  const CatalogLoaded({
    required this.categories,
    required this.catalogs,
  });
}

class CatalogEmpty extends CatalogState {
  final List<CategoryDto> categories;

  const CatalogEmpty({
    required this.categories,
  });
}

class CatalogError extends CatalogState {
  final String message;

  const CatalogError(this.message);
}

