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
        Schema::table('vendors', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('vendors', 'code_auto_generated')) {
                $table->boolean('code_auto_generated')->default(false)->after('vendor_code');
            }
            if (!Schema::hasColumn('vendors', 'vendor_email')) {
                $table->string('vendor_email')->nullable()->after('vendor_name');
            }
            if (!Schema::hasColumn('vendors', 'vendor_mobile')) {
                $table->string('vendor_mobile')->nullable()->after('vendor_email');
            }
            if (!Schema::hasColumn('vendors', 'activity_type')) {
                $table->string('activity_type')->nullable()->after('vendor_mobile');
            }
            if (!Schema::hasColumn('vendors', 'msme_classification')) {
                $table->string('msme_classification')->nullable()->after('activity_type');
            }
            if (!Schema::hasColumn('vendors', 'pin')) {
                $table->string('pin')->nullable()->after('city_name');
            }
            if (!Schema::hasColumn('vendors', 'file_path')) {
                $table->string('file_path')->nullable()->after('income_tax_type');
            }
            if (!Schema::hasColumn('vendors', 'active')) {
                $table->boolean('active')->default(true)->after('file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'code_auto_generated', 
                'vendor_email', 
                'vendor_mobile', 
                'activity_type',
                'msme_classification',
                'pin',
                'file_path',
                'active'
            ]);
        });
    }
};
