<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class TransactionRules implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if(!$this->bankCardCheck($value))
            $fail('شماره کارت معتبر نمی باشد.');
    }

    /**
     * Check validation of bank card
     *
     * شماره روی کارت های اعتباری یا کارت های بانکی شماره ای است 16 رقمی که
     * از سمت چپ رقم اول بیانگر نوع یا کاربرد کارت
     * (برای کارت های بانکی و اعتباری این رقم می تواند 4 یا 5 یا 6 باشد )
     * و 5 رقم بعدی بیانگر شماره شناسایی صادر کننده کارت و ارقام 7 تا 15 بیانگر
     *  شماره حساب یا شماره منحصر به فرد در مرکز صادرکننده کارت
     * و رقم آخر آن هم یک رقم کنترل است که از روی 15 رقم سمت چپ بدست می آید.
     * برای بررسی کنترل کد کافی است مجدد از روی 15 رقم سمت چپ صحت رقم کنترل را محاسبه کنیم
     *
     * برای محاسبه رقم کنترل از روی سایر ارقام ، از سمت چپ و با شروع از موقعیت 1 تا موقعیت 16 ،
     *  ارقام موقعیت فرد را در 2 و ارقام موقعیت زوج را در یک ضرب می کنیم،
     * اگر حاصل ضرب هر مرحله بیشتر از 9 شد 9 واحد از آن کم کنید تا یک رقمی شود
     *  و سپس اعداد حاصل را با هم جمع می کنیم.
     * اگر عدد حاصل از اجرای مرحله یک بر 10 بخش پذیر باشد ، شماره کارت صحیح قلمداد می شود
     * در غیر اینصورت شماره کارت صحیح نمی باشد
     *
     * source: http://www.webhostingtalk.ir/showthread.php?t=202847
     *
     *
     * @param  mixed $card
     * @param  mixed $irCard
     *
     * @return bool
     */
    private function bankCardCheck($card='', $irCard=true)
    {
        $card = (string) preg_replace('/\D/','',$card);
        $strlen = strlen($card);

        if ($irCard == true and $strlen != 16)
            return false;

        if ($irCard != true and ($strlen <  13 or $strlen > 19))
            return false;

        if (!in_array($card[0], [2,4,5,6,9]))
            return false;

        for($i=0; $i < $strlen; $i++)
        {
            $res[$i] = $card[$i];
            if (($strlen % 2) == ($i % 2))
            {
                $res[$i] *= 2;
                if ($res[$i] > 9)
                    $res[$i] -= 9;
            }
        }

        return array_sum($res) % 10 == 0 ? true : false;
    }
}
