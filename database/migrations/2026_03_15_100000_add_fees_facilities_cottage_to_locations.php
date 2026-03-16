<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('fees', 500)->nullable()->after('address');
            $table->text('facilities')->nullable()->after('fees');
            $table->string('cottage', 500)->nullable()->after('facilities');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['fees', 'facilities', 'cottage']);
        });
    }
};
