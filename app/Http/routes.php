<?php
//Route::group(['middleware'=>['web']], function() {






    /**
    *
    *login
    *
    */
     Route::get('auth/login', [

      'uses'=>'Auth\AuthController@getLogin',
      'as'=>'getLogin',

     ]);


     Route::post('auth/login',  [

          'uses'=>'Auth\AuthController@postLogin',
          'as' =>'postLogin',

     ]);


    /**
    *
    * logout
    *
    */
     Route::get('auth/logout', [

      'uses' =>'Auth\AuthController@getLogout',
      'as'=>'getLogout',

     ]);



    /**
    *
    * registration
    *
    */
     Route::get('auth/register', [

         'uses'=>'Auth\AuthController@getRegister',
         'as'=>'getRegister',

     ]);


     Route::post('auth/register', [

      'uses'=>'Auth\AuthController@postRegister',
      'as'=>'postRegister',

     ]);




Route::group(['middleware'=>['auth', 'no-cache']], function(){



    /**
    *
    * Personal
    *
    */
    Route::any('/auth/admin/new', [

            'as'=>'new',
            'uses'=>'personal@new'

    ]);



    Route::any('/auth/admin/edit/{tid?}/{print?}', [

            'as'=>'edit',
            'uses'=>'personal@edit'

    ]);



    /**
    *
    * Apptran
    *
    */

    Route::post('/auth/admin/searchkpp', [

            'as'=>'searchkpp',
            'uses'=>'personal@searchkpp'

    ]);


    

    /**
    *
    * Recount  SpecExp via date
    *
    */

    Route::post('/auth/admin/xspecexp', [

            'as'=>'xspecexp',
            'uses'=>'personal@xspecexp'

    ]);




    /**
    *
    * Archive: 
    @list, 
    @removie to archive, 
    @create new anket via archive anket
    *
    */


    Route::get('/auth/admin/archive', [

        'as'=>'archive',
        'uses'=>'personal@archive'

    ]);


    Route::post('/auth/admin/toArchive', [

        'as'=>'toArchive',
        'uses'=>'personal@toArchive'

    ]);

    Route::post('/auth/admin/fromArchive', [

        'as'=>'fromArchive',
        'uses'=>'personal@fromArchive'

    ]);



    /*Happy bithdays*/
    Route::get('/auth/admin/hb/{sd?}/{print?}', [

        'as'=>'hb',
        'uses'=>'personal@hb'

    ]);

     /*Anniversary*/
    Route::get('/auth/admin/ann/{print?}', [

        'as'=>'ann',
        'uses'=>'personal@ann'

    ]);



    /**
    *
    * admin
    *
    */
     Route::get('auth/admin/{print?}', [

         'uses'=>'personal@admin',
         'as'=>'admin'

     ]);





     // upload
    Route::post('/auth/admin/upload', [

        'as'=>'upload',
        'uses'=>'personal@upload'

    ]);



}); 



