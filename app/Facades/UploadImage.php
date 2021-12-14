<?php

namespace App\Facades;

 use Illuminate\Support\Facades\Facade;

class UploadImage extends Facade
{

    protected static function getFacadeAccessor() {
        return 'uploadimage';
    }

}
