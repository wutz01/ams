<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
  // USER LOGOUT
  Route::post('user/logout', 'UserController@userLogout');

  /*
   * USERS
   */
  // GET AUTHENTICATED User
  Route::get('getUserLogin', 'UserController@getUserLogin');
  // GET ALL USERS
  Route::get('user/all', 'UserController@getAllUsers');
  // VIEW USER
  Route::get('user/{id}', 'UserController@getUser');
  // UPDATE USER
  Route::post('user/update', 'UserController@updateUser');

  /*
   * PRODUCTS CATEGORY
   */

   Route::get('category/all', 'ProductController@allCategory');
   Route::get('category/{id}', 'ProductController@getCategory');
   Route::get('category/{catId}/delete', 'ProductController@deleteCategory');
   Route::post('category/update', 'ProductController@updateCategory');
   Route::post('category/create', 'ProductController@createCategory');

  /*
   * PRODUCTS SUPPLIER
   */

   Route::get('supplier/all', 'SupplierController@allSuppliers');
   Route::get('supplier/{id}', 'SupplierController@getSupplier');
   Route::post('supplier/update', 'SupplierController@updateSupplier');
   Route::post('supplier/create', 'SupplierController@createSupplier');
   Route::get('supplier/{supplierId}/delete', 'SupplierController@deleteSupplier');

   /*
    * PRODUCTS
    */
    Route::post('product/update', 'ProductController@updateProduct');
    Route::post('product/create', 'ProductController@createProduct');
    Route::post('product/upload/image', 'ProductController@uploadProductImage');
    Route::get('product/delete/image/{productId}', 'ProductController@deleteProductImage');
    Route::get('product/update/{productId}/status/{status}', 'ProductController@updateStatus');

   /*
    * CART
    */
    Route::get('cart/user/my-cart', 'CartController@getCartByUserId');
});

Route::post('user/login', 'UserController@authenticate');
Route::post('user/register', 'UserController@register');

// Generate UUID
Route::get('generate/guestID', 'ToolsController@generateUUID');

/*
 * PRODUCTS
 */

Route::get('product/all', 'ProductController@allProducts');
Route::get('product/{id}', 'ProductController@getProduct');

/*
 * CART
 */
Route::post('cart/add', 'CartController@addToCart');
Route::get('cart/guest/{guestId}', 'CartController@getCartByGuestId');
Route::get('cart/remove/item/{itemId}', 'CartController@removeToCart');
