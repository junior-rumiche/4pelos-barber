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
        if (Schema::hasColumn('order_service', 'quantity')) {
            return;
        }

        Schema::table('order_service', function (Blueprint $table): void {
            $table->unsignedInteger('quantity')->default(1)->after('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('order_service', 'quantity')) {
            return;
        }

        Schema::table('order_service', function (Blueprint $table): void {
            $table->dropColumn('quantity');
        });
    }
};
