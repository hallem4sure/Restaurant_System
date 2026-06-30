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
        Schema::table('bills', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('order_id');
            $table->string('payment_method')->nullable()->change();
            $table->decimal('amount_paid', 10, 2)->nullable()->change();
            $table->timestamp('paid_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('status');
            // Reverting nullable changes in SQLite can be complex,
            // we will leave them nullable for down migration or use nullable()->change() again
        });
    }
};
