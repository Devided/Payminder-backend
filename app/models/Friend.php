<?php

class Friend extends \Eloquent {
	protected $fillable = [];

    public function payminder(){
        return $this->belongsTo('Payminder');
    }

    public function number()
    {
        $number = preg_replace('/[^0-9]+/', '', $this->phonenumber);

        if($number[0] == "0" && $number[1] == "0")
        {
            $number = substr($number, 2);
        } else if($number[0] == "0" && $number[1] == "6")
        {
            $number = substr($number, 1);
            $number = "31" . $number;
        }

        return $number;
    }

    public static function transformNumber($t)
    {
        $number = preg_replace('/[^0-9]+/', '', $t);

        if($number[0] == "0" && $number[1] == "0")
        {
            $number = substr($number, 2);
        } else if($number[0] == "0" && $number[1] == "6")
        {
            $number = substr($number, 1);
            $number = "31" . $number;
        }

        return $number;
    }

    public function paid()
    {
        if($this->paid == 1)
        {
            return true;
        }
        elseif($this->paid == 0)
        {
            return false;
        }

    }

    public static function sendsmsold($id){
        Log::info("sendsms call started");


        $friend = Friend::find($id);
        $payminder = Payminder::find($friend->payminder_id);
        if(!$friend->paid){
            // MASK: SEND PAYMINDERS
            $user = "payminder";
            $password = "bHI7s2DG";
            $api_id = "3503724";
            $baseurl ="http://api.clickatell.com";
            // auth call
            $url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";
            // do auth call
            $ret = file($url);
            // explode our response. return string is on first line of the data returned
            $sess = explode(":",$ret[0]);
            if ($sess[0] == "OK") {
                $msg = "";
                if($payminder->description == "" && $friend->amount == "0")
                {
                    $msg = "";
                } else if($payminder->description == "" && $friend->amount != "0")
                {
                    $msg = " (" . $payminder->description . ")";
                } else if($payminder->description != "" && $friend->amount == "0")
                {
                    $msg = " (" . $friend->amount . " euro)";
                } else {
                    $msg = " (" . $friend->amount . " euro, ".$payminder->description.")";
                }

                $reknr = "";
                if($payminder->sender_iban != ""){
                    $reknr = ". Het rekeningnummer is " . $payminder->sender_iban . "";
                }

                $message = "Beste " . $friend->first_name . ",\n\n" . $payminder->sender_name . " heeft geld voorgeschoten" . $msg . $reknr . ". Heb jij al betaald? Klik hier: http://api.payminder.nl/c/" . $friend->id . " \n\nNog geen tijd gehad? Geen probleem, ik stuur je morgen weer een berichtje.\n\nGroeten, Bill Cashback\n\nOok je vrienden automatisch herinneren?\nDownload Payminder: bit.ly/10ZNepH";

                $text = urlencode($message);
                $to = $friend->number();

                $sess_id = trim($sess[1]); // remove any whitespace
                $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=Payminder";

                // do sendmsg call
                $ret = file($url);
                $send = explode(":",$ret[0]);

                if ($send[0] == "ID") {
                    //echo "successnmessage ID: ". $send[1];
                } else {
                    //echo "send message failed";
                }

                $date = \Carbon\Carbon::now()->addHours(24);
                //Queue::later($date, 'sendsms@send', ['id' => $id]);

                Queue::later($date, function($job) use ($id){
                    Friend::sendsms($id);
                });


                Log::info("send sms for user: ".$friend->first_name);
            } else {
                //echo "Authentication failure: ". $ret[0];
            }
        }
    }

    public static function sendsms($id)
    {
        $friend = Friend::find($id);
        $payminder = Payminder::find($friend->payminder_id);

        $check = Friend::where('paid', '=', 1)->where('phonenumber', '=', $friend->number())->first();

        if($check == null)
        {
            // send firsttime message
            $message = "Hai ".$friend->first_name.", leuk avondje gehad?! Payminder hier, de nieuwe betaal app. Voeg dit 06-nr toe aan je contacten, want ".$payminder->sender_name." wil jou een Payminder sturen (met linkje).";
            WA::sendMessage($friend->number(), $message);
        }

        Queue::push(function($job) use ($id){
            Friend::sendsms2($id);
        });
    }

    public static function sendsms2($id)
    {
        Log::info("sendsms call started");

        $friend = Friend::find($id);
        $payminder = Payminder::find($friend->payminder_id);

        if($friend->paid)
        {
            return;
        }

        $msg = "";
        if($payminder->description == "" && $friend->amount == "0")
        {
            $msg = "";
        } else if($payminder->description == "" && $friend->amount != "0")
        {
            $msg = " (" . $payminder->description . ")";
        } else if($payminder->description != "" && $friend->amount == "0")
        {
            $msg = " (" . $friend->amount . " euro)";
        } else {
            $msg = " (" . $friend->amount . " euro, ".$payminder->description.")";
        }

        $reknr = "";
        if($payminder->sender_iban != ""){
            $reknr = ". Het rekeningnummer is " . $payminder->sender_iban . "";
        }

        $message = "Beste " . $friend->first_name . ",\n\n" . $payminder->sender_name . " heeft geld voorgeschoten" . $msg . $reknr . ". Heb jij al betaald? Klik hier: http://api.payminder.nl/c/" . $friend->id . " \n\nNog geen tijd gehad? Geen probleem, ik stuur je morgen weer een berichtje.\n\nGroeten, Bill Cashback\n\nOok je vrienden automatisch herinneren?\nDownload Payminder: bit.ly/10ZNepH";

        $description = "";
        if($payminder->description != "" || $payminder->description != null)
        {
            $description = "Omschrijving: ".$payminder->description;
        }

        $bedrag = "het bedrag";
        if($friend->amount != "0")
        {
            $bedrag = "€".$friend->amount;
        }

        $msg1 = "Payminder: " . $payminder->sender_name . " heeft “the bill” betaald.\n".$description." \n\nAl betaald? Yes, Bill! Ik maak altijd meteen geld over. Klik hier:  http://api.payminder.nl/c/" . $friend->id . "\nZo niet. Zou je ".$bedrag." kunnen overmaken aan ".$payminder->sender_name."? Top!";
        $msg2 = $payminder->sender_iban;

        WA::sendMessage($friend->number(), $msg1);

        if($payminder->sender_iban != "" || $payminder->sender_iban != null)
        {
            $nr = $friend->number();
            $iban = $payminder->sender_iban;

            Queue::push(function($job) use ($nr,$iban){
                Friend::sendIBAN($nr,$iban);
            });
        }

        $date = \Carbon\Carbon::now()->addHours(24);
        //Queue::later($date, 'sendsms@send', ['id' => $id]);

        Queue::later($date, function($job) use ($id){
            Friend::sendsms($id);
        });

        Log::info("sent sms for user: ".$friend->first_name);
    }

    public static function sendIBAN($nr,$iban)
    {
        WA::sendMessage($nr, $iban);

        Queue::push(function($job) use ($nr){
            Friend::lastCall($nr);
        });
    }

    public static function lastCall($nr)
    {
        $msg = "Groeten, Bill Cashback\n\nPS: Lukt het nou echt niet; dan stuur ik je morgen weer een berichtje.. :)";
        WA::sendMessage($nr, $msg);
    }


}
