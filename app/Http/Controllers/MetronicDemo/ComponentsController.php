<?php

namespace App\Http\Controllers\MetronicDemo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComponentsController extends Controller
{
    public function accordion()
    {
        return Inertia::render('MetronicDemo/Components/Accordion');
    }
}
