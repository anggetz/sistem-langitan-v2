<?php

use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\Cache;

function pt()
{
    if (app()->bound('pt')) {
        return app('pt');
    }
    $defaultPt = Cache::get("pt", null);
    if ($defaultPt == null) {
        $defaultPt = App\Models\PerguruanTinggi::find(env('APP_ID_PERGURUAN_TINGGI_DEFAULT'));
        Cache::put("pt", $defaultPt, 600);
    }

    return $defaultPt;
}
