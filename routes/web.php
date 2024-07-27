<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['two_factor']], function () {
        Route::get('/', 'AppController@index')->name('siteurl');

        Route::group(['prefix' => 'user-setup', 'as' => 'user-setup.'], function () {
            Route::resource('user',         'UserController')->middleware('can:view_users');
        });
        Route::get('/listuser',             'UserController@ajaxData')->name('get.user')->middleware('can:view_users');

        Route::get('/permission',           'AppController@permission')->name('permission')->middleware('can:view_permissions');
        Route::get('/permission-list',      'AppController@permissionlist')->name('permission.list');
        Route::get('/role',                 'AppController@role')->name('role')->middleware('can:view_roles');
        Route::post('/getroles',            'AppController@getroles')->name('get.roles');
        Route::post('/addroles',            'AppController@saverole')->name('add.roles')->middleware('can:edit_roles');
        Route::delete('/deleteroles',       'AppController@deleteroles')->name('delete.roles')->middleware('can:edit_roles');
        Route::post('/getmenuoptionroles',  'AppController@menuoptionroles')->name('get.roles.menu');
        Route::post('/getlinesroles',       'AppController@lineroles')->name('get.roles.line');
        Route::post('/gethakakses',         'AppController@hakakses')->name('get.hakakses');
        Route::post('/gethakakses2',        'AppController@hakakses2')->name('get.hakakses2');
        Route::post('/addhakakses',         'AppController@addhakakses')->name('add.hakakses')->middleware('can:edit_roles');
        Route::post('/removehakakses',      'AppController@removehakakses')->name('remove.hakakses')->middleware('can:edit_roles');

        //others
        Route::get('get-button-option',     'AjaxController@getButtonOption')->name('get.button-option');
        Route::get('set-dark-theme',        'AppController@toggletheme')->name('toggle.theme');
        Route::post('changepassword',       'AppController@changepassword')->name('changepassword');

        //MASTER
        Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
            Route::group(['prefix' => 'package', 'as' => 'package.', 'middleware' => 'can:master_package'], function () {
                Route::get('get-data',      'Master\PackageController@ajaxData')->name('get-data');
            });
            Route::resource('package',      'Master\PackageController')->middleware('can:master_package');

            Route::group(['prefix' => 'addon', 'as' => 'addon.', 'middleware' => 'can:master_addon'], function () {
                Route::get('get-data',      'Master\AddonController@ajaxData')->name('get-data');
            });
            Route::resource('addon',        'Master\AddonController')->middleware('can:master_addon');

            Route::group(['prefix' => 'tipe-pembayaran', 'as' => 'tipe-pembayaran.', 'middleware' => 'can:master_tipe_pembayaran'], function () {
                Route::get('get-data',          'Master\TipePembayaranController@ajaxData')->name('get-data');
            });
            Route::resource('tipe-pembayaran',  'Master\TipePembayaranController')->middleware('can:master_tipe_pembayaran');
        });
        //MASTER


        ///TRANSACTION
        Route::group(['prefix' => 'transaction', 'as' => 'transaction.'], function () {
            Route::get('get-data',                      'TransactionController@ajaxData')->name('get-data');
            Route::get('get-data-ordered',              'TransactionController@ajaxDataOrdered')->name('get-data-ordered');
            Route::get('get-data-today',                'TransactionController@ajaxDataToday')->name('get-data-today');


            Route::get('menuoption',                    'TransactionController@menuoption')->name('menuoption');

            Route::get('lines',                         'TransactionController@lines')->name('lines');
            Route::get('lines/form',                    'TransactionController@formLines')->name('lines-form');
            Route::post('lines/add/{trans_id}',         'TransactionController@saveLines')->name('lines-add');
            Route::get('package/delete/{line_id}',      'TransactionController@deletePackage')->name('package-delete');
            Route::get('addon/delete/{line_id}',        'TransactionController@deleteAddon')->name('addon-delete');

            Route::post('process/{trans_id}',           'TransactionController@btnProcess')->name('process');

            Route::get('change-status/{trans_id}',      'TransactionController@changeStatus')->name('change-status');
            Route::get('unchange-status/{trans_id}',    'TransactionController@unChangeStatus')->name('unchange-status');

            Route::get('upload-url-form/{trans_id}',    'TransactionController@uploadUrlForm')->name('upload-url-form');
            Route::post('upload-url/{trans_id}',        'TransactionController@uploadUrl')->name('upload-url');
        });
        Route::resource('transaction',                  'TransactionController');
        ///TRANSACTION  

        ///KASIR
        Route::group(['prefix' => 'kasir', 'as' => 'kasir.', 'middleware' => 'can:kasir_view'], function () {
            Route::get('get-data',          'KasirController@ajaxData')->name('get-data');

            Route::get('menuoption',        'KasirController@menuoption')->name('menuoption');

            Route::get('print/{id}',        'KasirController@print')->name('print');
            Route::get('excel/{id}',        'KasirController@excel')->name('excel');

            Route::get('lines',             'KasirController@lines')->name('lines');
        });
        Route::resource('kasir', 'KasirController')->middleware('can:kasir_view');
        ///KASIR

        ///OPERATOR
        Route::group(['prefix' => 'operator', 'as' => 'operator.', 'middleware' => 'can:operator_access'], function () {
            Route::get('menuoption',                'OperatorController@menuoption')->name('menuoption');
        });

        Route::resource('operator', 'OperatorController')->middleware('can:operator_access');
        ///OPERATOR

        ///POS
        Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => 'can:pos_access'], function () {

            //kasir
            Route::get('open',                  'KasirController@openView')->name('open');
            Route::post('open-process',         'KasirController@openProcess')->name('open-process');
            Route::get('closed/{id}',           'KasirController@closedView')->name('closed');
            Route::post('closed-process/{id}',   'KasirController@closedProcess')->name('closed-process');
            //kasir

            Route::get('order-list',        'PosController@orderList')->name('order-list');
            Route::get('trans-today',       'PosController@transToday')->name('trans-today');

            Route::get('get-transaction',      'PosController@getTransaction')->name('get-transaction');
            Route::get('detail-transaction',      'PosController@detailTransaction')->name('detail-transaction');
            Route::get('choose-package',        'PosController@choosePackage')->name('choose-package');

            Route::get('addon/delete/{line_id}',    'PosController@deleteAddon')->name('addon-delete');

            Route::get('get-customer',     'PosController@getCustomer')->name('get-customer');

            Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
                Route::get('create',            'PosController@createCustomer')->name('create');
                Route::post('store',            'PosController@storeCustomer')->name('store');
            });
        });
        Route::resource('pos', 'PosController')->middleware('can:pos_access');
        ///POS

        //REPORT
        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('transaksi',  'ReportController@transaksiIndex')->name('transaksi.index')->middleware('can:report_transaksi');
            Route::post('transaksi/result',  'ReportController@transaksiResult')->name('transaksi.result')->middleware('can:report_transaksi');
        });

        //REPORT

    });

    Route::get('2fa', 'TwoFactorController@showTwoFactorForm');
    Route::post('2fa', 'TwoFactorController@verifyTwoFactor')->name('verifyTwoFactor');
});
