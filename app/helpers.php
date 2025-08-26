<?php

function pt()
{
    if (app()->bound('pt')) {
        return app('pt');
    }

    return App\Models\PerguruanTinggi::find(env('APP_ID_PERGURUAN_TINGGI_DEFAULT', 1));
}
