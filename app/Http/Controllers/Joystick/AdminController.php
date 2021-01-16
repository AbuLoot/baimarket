<?php

namespace App\Http\Controllers\Joystick;

use Illuminate\Http\Request;
use App\Http\Controllers\Joystick\Controller;

class AdminController extends Controller
{
    public function index()
    {
    	return view('joystick-admin.index');
    }

    public function filemanager()
    {
        if (!\Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('status', 'Ваши права ограничены!');
        }

    	return view('joystick-admin.filemanager');
    }

    public function frameFilemanager()
    {
    	return view('joystick-admin.frame-filemanager');
    }
}
