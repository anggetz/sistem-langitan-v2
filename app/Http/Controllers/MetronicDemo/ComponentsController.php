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

            case 'avatar':
                return Inertia::render('MetronicDemo/Components/Avatar');
                break;

            case 'badge':
                return Inertia::render('MetronicDemo/Components/Badge');
                break;

            case 'button':
                return Inertia::render('MetronicDemo/Components/Button');
                break;

            case 'button-group':
                return Inertia::render('MetronicDemo/Components/ButtonGroup');
                break;

            case 'card':
                return Inertia::render('MetronicDemo/Components/Card');
                break;

            case 'collapse':
                return Inertia::render('MetronicDemo/Components/Collapse');
                break;

            case 'container':
                return Inertia::render('MetronicDemo/Components/Container');
                break;

            case 'dismiss':
                return Inertia::render('MetronicDemo/Components/Dismiss');
                break;

            case 'drawer':
                return Inertia::render('MetronicDemo/Components/Drawer');
                break;

            case 'dropdown':
                return Inertia::render('MetronicDemo/Components/Dropdown');
                break;

            case 'modal':
                return Inertia::render('MetronicDemo/Components/Modal');
                break;

            case 'progress':
                return Inertia::render('MetronicDemo/Components/Progress');
                break;

            case 'rating':
                return Inertia::render('MetronicDemo/Components/Rating');
                break;

            case 'reparent':
                return Inertia::render('MetronicDemo/Components/Reparent');
                break;

            case 'scrollable':
                return Inertia::render('MetronicDemo/Components/Scrollable');
                break;

            case 'table':
                return Inertia::render('MetronicDemo/Components/Table');
                break;

            case 'theme':
                return Inertia::render('MetronicDemo/Components/Theme');
                break;

            case 'toggle':
                return Inertia::render('MetronicDemo/Components/Toggle');
                break;

            case 'tooltip':
                return Inertia::render('MetronicDemo/Components/Tooltip');
                break;

            default:
                abort(404, "'Component '$component' not defined");
                break;
        }

    }
}
