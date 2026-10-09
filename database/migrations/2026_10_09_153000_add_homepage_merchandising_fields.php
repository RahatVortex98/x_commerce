<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_trending')->default(false);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_recommended')->default(false);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('reviewer_name')->nullable();
            $table->boolean('is_featured')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['reviewer_name', 'is_featured']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_recommended');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_trending');
        });
    }
};
