<?php

use App\Models\PerguruanTinggi;
use Illuminate\Support\Facades\Cache;

if (! function_exists('pt')) {
    function pt()
    {
        if (app()->bound('pt')) {
            return app('pt');
        }

    $defaultPt = Cache::get("pt", null);
    if ($defaultPt == null) {
        $defaultPt = App\Models\PerguruanTinggi::find(config('app.id_perguruan_tinggi_default'));
        Cache::put("pt", $defaultPt, 600);
    }

        return $defaultPt;
    }
}
