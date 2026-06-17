<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->decimal('saturated_fat', 5, 1)->default(0)->after('fat');
            $table->decimal('sugar', 5, 1)->default(0)->after('saturated_fat');
            $table->decimal('fibre', 5, 1)->default(0)->after('sugar');
            $table->decimal('salt', 5, 2)->default(0)->after('fibre');
        });

        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->integer('calories')->default(2000);
            $table->decimal('protein', 5, 1)->default(150);
            $table->decimal('carbs', 5, 1)->default(250);
            $table->decimal('fat', 5, 1)->default(65);
            $table->decimal('saturated_fat', 5, 1)->default(20);
            $table->decimal('sugar', 5, 1)->default(30);
            $table->decimal('fibre', 5, 1)->default(30);
            $table->decimal('salt', 5, 2)->default(6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['saturated_fat', 'sugar', 'fibre', 'salt']);
        });
        Schema::dropIfExists('goals');
    }
};
