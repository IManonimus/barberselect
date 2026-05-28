import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:barberselect_mobile/app/app.dart';

void main() {
  testWidgets('App renders without error', (WidgetTester tester) async {
    await tester.pumpWidget(const BarberSelectApp());
    expect(find.byType(MaterialApp), findsOneWidget);
  });
}