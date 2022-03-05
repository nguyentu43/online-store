<?php

use Illuminate\Support\Facades\{Schema, DB};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('weight', 150)->nullable();
            $table->mediumText('description')->nullable();
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('product_type_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->string('slug', 150);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE products ADD COLUMN ts tsvector GENERATED ALWAYS AS (to_tsvector('english', name)) STORED");
        DB::statement('CREATE INDEX ts_idx ON products USING GIN (ts)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
