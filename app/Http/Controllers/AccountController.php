<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Rules\TransactionRules;

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
            'from' => ['required', 'numeric', 'min:16', 'max:16', new TransactionRules],
            'to' => ['required', 'min:16', 'numeric', 'max:16', new TransactionRules],
            'price' => ['required', 'numeric', 'min:1000', 'max:50000000'],
        ]);

        if($validatedData) {

            $card_info = Card::where('card_number', $request->input('from'));
            $balance = $card_info->price - $card_info->price - 500;

            if($balance > 0) {

                $result = $card_info->update(['price' => $balance]);

                if($result) {
                    $trans = new Transaction;
                    $trans->price = $request->price;
                    $trans->from = $this->numberToEn($request->from);
                    $trans->to = $this->numberToEn($request->to);
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

    private function numberToEn($number)
    {
        $range = range(0, 9);
        // Persian
        $persianDecimal = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        // Arabic
        $arabicDecimal = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        // number Arabic
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        // number Persian
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $string =  str_replace($persianDecimal, $range, $number);
        $string =  str_replace($arabicDecimal, $range, $number);
        $string =  str_replace($arabic, $range, $number);

        return str_replace($persian, $range, $string);
    }
}
