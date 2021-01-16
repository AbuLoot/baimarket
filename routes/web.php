<?php

Route::redirect('/admin', '/'.app()->getLocale().'/admin');

// Joystick Administration
Route::group(['prefix' => '{lang}/admin', 'middleware' => ['auth', 'role:admin|manager']], function () {

    Route::get('/', 'Joystick\AdminController@index');
    Route::get('filemanager', 'Joystick\AdminController@filemanager');
    Route::get('frame-filemanager', 'Joystick\AdminController@frameFilemanager');

    Route::resources([
        'categories' => 'Joystick\CategoryController',
        'pages' => 'Joystick\PageController',
        'section' => 'Joystick\SectionController',
        'posts' => 'Joystick\PostController',
        'products' => 'Joystick\ProductController',
        'slides' => 'Joystick\SlideController',
        'apps' => 'Joystick\AppController',
        'orders' => 'Joystick\OrderController',
        'options' => 'Joystick\OptionController',
        'modes' => 'Joystick\ModeController',
        'companies' => 'Joystick\CompanyController',
        'regions' => 'Joystick\RegionController',
        'roles' => 'Joystick\RoleController',
        'users' => 'Joystick\UserController',
        'permissions' => 'Joystick\PermissionController',
        'languages' => 'Joystick\LanguageController',
    ]);

    Route::get('categories-actions', 'Joystick\CategoryController@actionCategories');
    Route::get('companies-actions', 'Joystick\CompanyController@actionCompanies');

    Route::get('products-actions', 'Joystick\ProductController@actionProducts');
    Route::get('products-search', 'Joystick\ProductController@search');
    Route::get('products-category/{id}', 'Joystick\ProductController@categoryProducts');
    Route::get('products/{id}/comments', 'Joystick\ProductController@comments');
    Route::get('products/{id}/destroy-comment', 'Joystick\ProductController@destroyComment');
});

Route::redirect('/', '/'.app()->getLocale());

// Site
Route::group(['prefix' => '{lang}'], function () {

    // Authentication routes...
    // Route::get('cs-login', 'Auth\AuthCustomController@getLogin')->middleware('guest');;
    Route::post('cs-login', 'Auth\AuthCustomController@postLogin');

    // Registration routes...
    // Route::get('cs-register', 'Auth\AuthCustomController@getRegister')->middleware('guest');;
    // Route::post('cs-register', 'Auth\AuthCustomController@postRegister');
    // Route::get('confirm/{token}', 'Auth\AuthCustomController@confirm');

    // Registration routes...
    Route::get('login-and-register', 'Auth\AuthCustomController@getLoginAndRegister');

    Auth::routes();

    // News
    Route::get('i/news', 'NewsController@news');
    Route::get('news/{page}', 'NewsController@newsSingle');
    Route::post('comment-news', 'NewsController@saveComment');

    // Pages
    // Route::get('/', 'PageController@index');
    Route::get('i/catalog/{condition?}', 'PageController@catalog');
    Route::get('i/contacts', 'PageController@contacts');
    Route::get('i/{page}', 'PageController@page');

    // Input
    Route::get('search', 'InputController@search');
    Route::get('search-ajax', 'InputController@searchAjax');
    Route::post('send-app', 'InputController@sendApp');
    Route::post('filter-products', 'InputController@filterProducts');

    // User Profile
    Route::group(['middleware' => 'auth', 'role:user'], function() {

        Route::get('profile', 'ProfileController@profile');
        Route::get('profile/edit', 'ProfileController@editProfile');
        Route::post('profile', 'ProfileController@updateProfile');
        Route::get('orders', 'ProfileController@myOrders');
    });

    // Comments
    Route::post('review', 'CommentController@saveReview');
    Route::post('comment', 'CommentController@saveComment');

    // Cart Actions
    Route::get('cart', 'CartController@cart');
    Route::get('add-to-cart/{id}', 'CartController@addToCart');
    Route::get('remove-from-cart/{id}', 'CartController@removeFromCart');
    Route::get('clear-cart', 'CartController@clearCart');
    Route::post('store-order', 'CartController@storeOrder');
    Route::get('destroy-from-cart/{id}', 'CartController@destroy');

    // Favourite Actions
    Route::get('favorite', 'FavouriteController@getFavorite');
    Route::get('toggle-favourite/{id}', 'FavouriteController@toggleFavourite');

    // Shop
    Route::get('/', 'ShopController@index');
    Route::get('brand/{company}', 'ShopController@brandProducts');
    Route::get('brand/{company}/{category}/{id}', 'ShopController@brandCategoryProducts');
    Route::get('{category}/c-{id}', 'ShopController@categoryProducts');
    Route::get('{category}/{subcategory}/c-{id}', 'ShopController@subCategoryProducts');
    Route::get('{product}/p-{id}', 'ShopController@product');
    Route::post('comment-product', 'ShopController@saveComment');
});
