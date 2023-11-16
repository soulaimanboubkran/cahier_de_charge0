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

Schema::create('orders', function (Blueprint $table) {
    $table->bigIncrements('id');

    $table->string('order_number');
    $table->unsignedBigInteger('user_id');
    $table->enum('status', ['pending','processing','completed','decline'])->default('pending');
    $table->float('grand_total');
    $table->integer('item_count');
    $table->boolean('is_paid')->default(false);
    $table->enum('payment_method', ['cash_on_delivery', 'paypal','stripe','card'])->default('cash_on_delivery');

    $table->string('shipping_fullname');
    $table->string('shipping_address');
    $table->string('shipping_city');
    $table->string('shipping_state');
    $table->string('shipping_zipcode');
    $table->string('shipping_phone');
    $table->string('notes')->nullable();

    $table->string('billing_fullname');
    $table->string('billing_address');
    $table->string('billing_city');
    $table->string('billing_state');
    $table->string('billing_zipcode');
    $table->string('billing_phone');

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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

Schema::create('sub_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->onDelete('cascade');
    $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
    $table->enum('status', ['pending', 'processing', 'completed', 'decline'])->default('pending');
    $table->float('grand_total');
    $table->integer('item_count');
    $table->timestamps();
});

//----------------------------------------------------------------
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
Schema::create('attributes', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
//----------------------------------------------------------------------
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


/*
1. categories Table:
Purpose: Stores information about product categories.
Observations:
The foreign key parent_id establishes a hierarchical relationship among categories.
The order field might represent the display order of categories.
The name field holds the name of the category.
The slug field is unique and can be used for SEO-friendly URLs.
Overall, the table seems well-structured for category management.
2. shops Table:
Purpose: Represents information about individual shops/sellers.
Observations:
Foreign key user_id establishes a relationship with the users table, indicating the owner of the shop.
Includes fields such as is_active, description, and rating to capture shop-related details.
Overall, the table appears to cover the essential aspects of a shop.
3. products Table:
Purpose: Stores information about products.
Observations:
Foreign key shop_id links products to a specific shop.
Includes standard fields like name, description, price, and cover_img.
A product_attributes field is provided as JSON, allowing for flexible attribute storage.
Appears well-structured for managing product details.
4. orders Table:
Purpose: Represents customer orders.
Observations:
Includes fields for order details like order_number, status, grand_total, etc.
Separate shipping and billing information is included.
The payment_method field allows for multiple payment methods.
user_id establishes a relationship with the users table.
The addition of paypal_orderid allows tracking PayPal order IDs.
Overall, comprehensive for managing order-related information.
5. order_items Table:
Purpose: Captures individual items within an order.
Observations:
Foreign keys link to the products and orders tables.
Fields include price and quantity.
Well-structured for tracking products within an order.
6. coupons Table:
Purpose: Stores information about discount coupons.
Observations:
Standard fields such as name, code, type, value, and description.
Well-designed for managing coupon details.
7. sub_orders Table:
Purpose: Represents sub-orders (potentially for multi-seller scenarios).
Observations:
Foreign keys link to the orders and users tables.
Fields include status, grand_total, and item_count.
Well-suited for managing sub-orders.
8. sub_order_items Table:
Purpose: Captures individual items within a sub-order.
Observations:
Foreign keys link to the sub_orders and products tables.
Fields include price and quantity.
Suitable for tracking products within a sub-order.
9. transactions Table:
Purpose: Stores information about financial transactions.
Observations:
Foreign key links to the sub_orders table.
Includes details like transaction_id, amount_paid, and commission.
Suitable for tracking transaction details.
10. attributes Table:
Purpose: Represents product attributes.
Observations:
Fields include name for attribute names.
Suitable for managing different attributes.
11. attribute_values Table:
Purpose: Captures values associated with product attributes.
Observations:
Foreign key links to the attributes table.
Fields include value.
Well-structured for managing attribute values.
*/