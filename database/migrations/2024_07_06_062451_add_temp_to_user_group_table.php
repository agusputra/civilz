<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            // Only used for testing
            $table->string('tmp')->nullable()->after('meta');
            // Only used for testing
            $table->json('tmp_json')->nullable()->after('tmp');
        });

        // Schema::table('users', function (Blueprint $table) {
        //     $table->date('dob')->nullable()->after('email');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            $table->dropColumn(['tmp', 'tmp_json']);
        });

        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn(['dob']);
        // });
    }
};
