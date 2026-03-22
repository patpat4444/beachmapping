<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('feature_requests') && Schema::hasTable('locations')) {
            try {
                Schema::table('feature_requests', function (Blueprint $table) {
                    $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
                });
            } catch (\Throwable $e) {
                // If the foreign key already exists or another error occurs, ignore to keep migration idempotent
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('feature_requests')) {
            try {
                Schema::table('feature_requests', function (Blueprint $table) {
                    $table->dropForeign(['location_id']);
                });
            } catch (\Throwable $e) {
                // Ignore if the foreign key doesn't exist
            }
        }
    }
};
