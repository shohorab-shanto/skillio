<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class UserOnBoardingController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('frontend.user.login');
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('frontend.user.register');
    }
    public function categoryService()
    {
        return view('frontend.user.category_service');
    }

    public function inPersonOrOnline()
    {
        return view('frontend.user.in_person_or_online');
    }

    public function inPersonEducationLocation()
    {
        return view('frontend.user.in_person_education_location');
    }

    public function onlineEducation()
    {
        return view('frontend.user.online_education');
    }
}