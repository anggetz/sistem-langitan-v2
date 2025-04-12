<?php

namespace App\Http\Controllers\MetronicDemo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplatesController extends Controller
{
    public function __invoke($page)
    {
        Inertia::share('useDemoSidebar', true);

        switch ($page) {

            case 'blank':
                return Inertia::render('MetronicDemo/Templates/Blank');
                break;

            case 'table':
                return Inertia::render('MetronicDemo/Templates/Table');
                break;

            case 'filter-table':
                return Inertia::render('MetronicDemo/Templates/FilterTable');
                break;

            case 'upload':
                return Inertia::render('MetronicDemo/Templates/Upload');
                break;

            default:
                abort(404, "Page '$page' not defined");
                break;
        }
    }
}
