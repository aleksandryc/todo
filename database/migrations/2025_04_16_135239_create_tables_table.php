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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Big family table"
            $table->string('color')->nullable(); // Color can be null befor painting
            $table->string('material'); // e.g., "Wood", "Plastic", "Metal"
            $table->string('status')->default('in_aceptance'); // Status: 'in_aceptance', 'in_painting', 'in_assembly', 'in_delivery', 'completed'
            $table->decimal('price', 8, 2); // Price of the table
            $table->foreignId('orders_id')->constrained('orders')->onDelete('cascade'); // Foreign key to the orders table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
