<?php

use App\Models\PerguruanTinggi;

function pt()
{
    if (app()->bound('pt')) {
        return app('pt');
    }
    return PerguruanTinggi::find(env('APP_ID_PERGURUAN_TINGGI_DEFAULT'));
}
