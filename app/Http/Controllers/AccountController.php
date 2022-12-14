<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Rules\TransactionRules;
use App\Contracts\SendSms\SendSms;
use Symfony\Component\Console\Input\Input;

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
        $trans = Transaction::where('created_at', '>=', $formatted_date)
                                ->pluck('id')->toArray();

        $users_info = DB::table('cards')
            ->select('user_id', DB::raw('count(id) as number_of_use'))
            ->whereIn('id', $trans)
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
    public function makeTransaction(Request $request)
    {
        $validatedData = $request->validate([
            'from' => ['required', 'numeric', 'min:16', 'max:16', new TransactionRules],
            'to' => ['required', 'min:16', 'numeric', 'max:16', new TransactionRules],
            'price' => ['required', 'numeric', 'min:1000', 'max:50000000'],
        ]);

        if($validatedData) {

            $card_info = Card::where('card_number', $request->input('from'));
            $balance = $card_info->price - $request->price - 500;

            if($balance > 0) {

                $result = $card_info->update(['price' => $balance]);

                if($result) {

                    $from_msg = '???????? ?????? ?????? ???? ???????? ??????:'. $request->price - 500 .'.???????????? ???? ???????????? ?????????? ????.';
                    $to_msg = '???????? ?????????? ?????? ???? ???????? ??????:'. $request->price  .'.?????????? ???? ???????????? ?????????? ????.';

                    $sms_from = new SendSms(
                        'https://api.kavenegar.com/v1/{API-KEY}/sms/send.json',
                        $from_msg,
                        $this->numberToEn($request->to),
                        $this->numberToEn($request->from)
                    );
                    $sms_from->sendSms();

                    $sms_to = new SendSms(
                        'https://api.kavenegar.com/v1/{API-KEY}/sms/send.json',
                        $to_msg,
                        $this->numberToEn($request->to),
                        $this->numberToEn($request->from)
                    );
                    $sms_to->sendSms();

                    $trans = new Transaction;
                    $trans->price = $request->price;
                    $trans->from = $this->numberToEn($request->from);
                    $trans->to = $this->numberToEn($request->to);
                    $trans->save();

                    if($trans) {
                        return response()->json($trans->toArray(),
                            ['message' => '???????????? ???? ???????????? ?????????? ????.']);
                    } else {
                        return response()->json([
                            'message' => '?????? ???????????? ???? ???????? ?????????? ????.'
                        ]);
                    }
                }

            } else {
                return response()->json([
                    'message' => '???????????? ???????? ????????.'
                ]);
            }
        }
    }

    /**
     * Change number to eng
     *
     * @param  mixed $number
     *
     * @return string
     */
    private function numberToEn($number)
    {
        $range = range(0, 9);
        // Persian
        $persianDecimal = ['??', '??', '??', '??', '??', '??', '??', '??', '??', '??'];
        // Arabic
        $arabicDecimal = ['??', '??', '??', '??', '??', '??', '??', '??', '??', '??'];
        // number Arabic
        $arabic = ['??', '??', '??', '??', '??', '??', '??', '??', '??', '??'];
        // number Persian
        $persian = ['??', '??', '??', '??', '??', '??', '??', '??', '??', '??'];

        $string =  str_replace($persianDecimal, $range, $number);
        $string =  str_replace($arabicDecimal, $range, $number);
        $string =  str_replace($arabic, $range, $number);

        return str_replace($persian, $range, $string);
    }
}
