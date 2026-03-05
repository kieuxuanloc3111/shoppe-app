<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\History;
class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::orderBy('id', 'desc')->get();

        return view('admin.history.index', compact('histories'));
    }
}