<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visit_line_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('fdi_number')->nullable()->after('visit_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('check_number', 64)->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('visit_line_items', function (Blueprint $table) {
            $table->dropColumn('fdi_number');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('check_number');
        });
    }
};
