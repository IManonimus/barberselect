class UserDto {
  final int id;
  final String name;
  final String email;
  final bool isAdmin;

  const UserDto({
    required this.id,
    required this.name,
    required this.email,
    required this.isAdmin,
  });

  factory UserDto.fromJson(Map<String, dynamic> json) {
    return UserDto(
      id: (json['id'] as num).toInt(),
      name: (json['name'] ?? '') as String,
      email: (json['email'] ?? '') as String,
      isAdmin: (json['is_admin'] ?? json['isAdmin'] ?? false) as bool,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'is_admin': isAdmin,
    };
  }
}

