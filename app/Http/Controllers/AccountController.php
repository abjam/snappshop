<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Carbon\Carbon;

class AccountController extends Controller
{
    /**
     * Get last ten records of transaction table
     *
     * @return JSON
     */
    public function lastTen()
    {
        $trans = Transaction::orderBy('id', 'desc')->take(10)->get();

        return response()->json($trans->toArray());
    }

    /**
     * Get last three users of transactions that send since 10 minutes until now
     *
     * @return JSON
     */
    public function threeUsersLastTen()
    {
        $formatted_date = Carbon::now()->subMinutes(10)->toDateTimeString();
        $trans = Transaction::where('created_at', '>=', $formatted_date)->get();

        return response()->json($trans->toArray());
    }
}
