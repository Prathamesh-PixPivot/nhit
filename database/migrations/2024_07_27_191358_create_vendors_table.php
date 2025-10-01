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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('s_no')->nullable();
            $table->string('from_account_type')->nullable();
            $table->string('project')->nullable();
            $table->string('account_name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('parent')->nullable();
            $table->string('account_number')->nullable();
            $table->string('name_of_bank')->nullable();
            $table->string('ifsc_code_id')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('vendor_type')->nullable();
            $table->string('vendor_code')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_nick_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('gstin')->unique()->nullable();
            $table->string('pan')->unique()->nullable();
            $table->string('country_id')->nullable();
            $table->string('state_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('country_name')->nullable();
            $table->string('state_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('msme')->nullable();
            $table->string('msme_registration_number')->nullable();
            $table->timestamp('msme_start_date')->nullable();
            $table->timestamp('msme_end_date')->nullable();
            $table->string('material_nature')->nullable();
            $table->string('gst_defaulted')->nullable();
            $table->string('section_206AB_verified')->nullable();
            $table->string('benificiary_name')->nullable();
            $table->string('remarks_address')->nullable();
            $table->string('common_bank_details')->nullable()->comment('Common Bank Details Required For Location Level Or Not');
            $table->string('income_tax_type')->nullable();
            $table->timestamp('date_added')->useCurrent()->nullable();
            $table->timestamp('last_updated')->useCurrent()->useCurrentOnUpdate()->nullable();
            // $table->timestamp('vendor_name')->useCurrent()->nullable();
            // $table->timestamp('date')->useCurrent()->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
