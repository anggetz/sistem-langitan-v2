<?php

namespace App\Http\Controllers\MetronicDemo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComponentsController extends Controller
{
    public function __invoke($component)
    {
        Inertia::share('useDemoSidebar', true);

        switch ($component) {
            case 'accordion':
                return Inertia::render('MetronicDemo/Components/Accordion');
                break;

            default:
                abort(404, "'Component '$component' not defined");
                break;
        }

    }
}
