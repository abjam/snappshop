<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function makeTransaction(Request $request) {

        $from = $request->input('from');
        $to = $request->input('to');
        $price = $request->input('price');


    }
}
