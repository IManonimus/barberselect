<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->string('care_level')->nullable()->after('description');
            $table->string('face_shape')->nullable()->after('care_level');
            $table->string('hair_type')->nullable()->after('face_shape');
            $table->text('tips')->nullable()->after('hair_type');
        });
    }

    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn(['care_level', 'face_shape', 'hair_type', 'tips']);
        });
    }
};

