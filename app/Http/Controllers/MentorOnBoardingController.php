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
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        abort(404);
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        abort(404);
    }

}
