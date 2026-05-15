<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class MapController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('map.index', compact('categories'));
    }
}