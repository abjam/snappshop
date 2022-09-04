<?php

namespace App\Http\Controllers;

use App\Models\Card;
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

        $users_info = DB::table('cards')
            ->select('user_id', DB::raw('count(id) as number_of_use'))
            ->whereIn('id', $trans->toArray())
            ->groupBy('user_id')
            ->orderByDesc('number_of_use')
            ->take(3)
            ->get();

        return response()->json($users_info->toArray());
    }

    /**
     * Make transaction and update balance of account
     *
     * @param  mixed $request
     *
     * @return JSON
     */
    public function makeTransaction(Request $request) {

        $validatedData = $request->validate([
            'from' => ['required', 'numeric', 'min:16', 'max:16'],
            'to' => ['required', 'min:16', 'numeric', 'max:16'],
            'price' => ['required', 'numeric'],
            'card_number' => ['required', 'numeric'],
            'card_id' => ['required', 'numeric'],
        ]);

        if($validatedData) {

            $card_info = Card::find($request->input($request->input('card_id')));
            $balance = $card_info->price - $card_info->price;

            if($balance > 0) {

                $result = $card_info->update(['price' => $balance]);

                if($result) {
                    $trans = new Transaction;
                    $trans->card_id = $request->card_id;
                    $trans->card_number = $request->card_number;
                    $trans->from = $request->from;
                    $trans->to = $request->to;
                    $trans->save();

                    if($trans) {
                        return response()->json($trans->toArray(),
                            ['message' => 'تراکنش با موفقیت انجام شد.']);
                    } else {
                        return response()->json([
                            'message' => 'ثبت تراکنش با شکست مواجه شد.'
                        ]);
                    }
                }

            } else {
                return response()->json([
                    'message' => 'موجودی کافی نیست.'
                ]);
            }
        }
    }
}
