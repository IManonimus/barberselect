part of 'ai_cubit.dart';

abstract class AiState {
  const AiState();
}

class AiInitial extends AiState {
  const AiInitial();
}

class AiLoading extends AiState {
  const AiLoading();
}

class AiSuccess extends AiState {
  final AiRecommendResponseDto response;

  const AiSuccess({required this.response});
}

class AiError extends AiState {
  final String message;

  const AiError(this.message);
}

