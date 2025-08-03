<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\CountryList;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Auth;


class MentorOnBoardingController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('frontend.mentor.on-boarding.login');
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('frontend.mentor.on-boarding.register');
    }

}
