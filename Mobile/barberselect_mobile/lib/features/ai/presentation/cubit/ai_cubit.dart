import 'package:flutter_bloc/flutter_bloc.dart';

import '../../data/ai_repository.dart';
import '../../data/models/ai_recommend_response_dto.dart';

part 'ai_state.dart';

class AiCubit extends Cubit<AiState> {
  final AiRepository repository;

  AiCubit({required this.repository}) : super(AiInitial());

  Future<void> recommend(String query) async {
    emit(AiLoading());
    try {
      final resp = await repository.recommend(query: query);
      emit(AiSuccess(response: resp));
    } catch (e) {
      emit(AiError(e.toString()));
    }
  }
}

