<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show Passport clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function clients()
    {
        return view('clients');
    }

    /**
     * Show personal access tokens.
     *
     * @return \Illuminate\Http\Response
     */
    public function tokens()
    {
        return view('tokens');
    }
}
