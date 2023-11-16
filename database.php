<?php 


 Schema::create('categories', function (/*Blueprint*/ $table) {
    $table->increments('id');
    $table->integer('parent_id')->unsigned()->nullable()->default(null);
    $table->foreign('parent_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('set null');
    $table->integer('order')->default(1);
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
//-------------------------------------------------------

Schema::create('shops', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->unsignedBigInteger('user_id');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

    $table->boolean('is_active')->default(false);

    $table->text('description')->nullable();
    $table->float('rating')->nullable();
    $table->timestamps();
});

//-----------------------------------------------------

Schema::create('products', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->mediumText('description');
    $table->float('price');
    $table->string('cover_img')->nullable();
    $table->unsignedBigInteger('shop_id')->nullable();
    $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');

    $table->timestamps();
});

//-------------------------------------------------------

Schema::create('order_items', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedBigInteger('order_id');
    $table->unsignedBigInteger('product_id');

    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

    $table->float('price');
    $table->integer('quantity');

    $table->timestamps();
});

//---------------------------------------------------------------


Schema::create('order_items', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->unsignedBigInteger('order_id');
    $table->unsignedBigInteger('product_id');

    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

    $table->float('price');
    $table->integer('quantity');

    $table->timestamps();
});

//-----------------------------------------------------------------


Schema::create('coupons', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code');
    $table->string('type');
    $table->string('value');
    $table->string('description')->nullable();
    $table->timestamps();
});

//------------------------------------------------------------------

Schema::create('sub_order_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('sub_order_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');

    $table->float('price');
    $table->integer('quantity');
});

//------------------------------------------------------------------


Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sub_order_id')->constrained('sub_orders')->onDelete('cascade');
    $table->string('transaction_id');
    $table->float('amount_paid');
    $table->float('commission');
    $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
    $table->timestamps();
});


//-------------------------------------------------------------------

Schema::create('attribute_values', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('attribute_id');
    $table->string('value');
    $table->timestamps();
});

//-----------------------------------------------------------------

 //this is add_attribute_column_to_products_table.php
Schema::table('products', function (Blueprint $table) {
    $table->json('product_attributes')->nullable();
});

//----------------------------------------------------------------

  // this is _add_paypal_order_id_to_orders_table.php
Schema::table('orders', function (Blueprint $table) {
    $table->string('paypal_orderid')->nullable();
});

