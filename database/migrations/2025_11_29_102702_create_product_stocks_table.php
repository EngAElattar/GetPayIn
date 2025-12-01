<?php

use App\Enums\Stock\StockDirection;
use Illuminate\Support\Facades\Schema;
use App\Enums\Stock\StockReferenceType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', StockDirection::values());
            $table->unsignedBigInteger('qty');
            $table->enum('reference_type', StockReferenceType::values())->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
