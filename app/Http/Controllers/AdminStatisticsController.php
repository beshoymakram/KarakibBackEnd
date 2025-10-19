<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        $usersCount = User::all()->count();
    }
}
