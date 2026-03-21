<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beach_owner_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('business_name');
            $table->text('business_address');
            $table->string('bir_document'); // Path to BIR certificate
            $table->string('business_permit')->nullable(); // Path to business permit
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Created user after approval
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beach_owner_applications');
    }
};
