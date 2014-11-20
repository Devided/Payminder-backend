<?php

class PaymindersController extends \BaseController {

	/**
	 * Display the specified resource.
	 * GET /payminders/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function send($payload)
	{
        $input = json_decode(base64_decode($payload));

        $payminder = new Payminder();
        $payminder->sender_name = $input->sender;
        $payminder->sender_iban = $input->iban;
        $payminder->pushID = $input->pushNotificationID;
        $payminder->start_time = intval($input->startTime);
        $payminder->end_time = intval($input->sendTime);
        $payminder->ip_address = Request::getClientIp();
        $payminder->description = $input->description_p;
        $payminder->save();

        $payminder->hash = Hash::make($payminder->id . microtime());
        $payminder->save();


        // MASK: SEND PAYMINDERS
        $user = "payminder";
        $password = "BEtYHsR1";
        $api_id = "3503724";
        $baseurl ="http://api.clickatell.com";
        // auth call
        $url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";
        // do auth call
        $ret = file($url);
        // explode our response. return string is on first line of the data returned
        $sess = explode(":",$ret[0]);
        if ($sess[0] == "OK") {
            foreach($input->personList as $friendinput)
            {
                $friend = new Friend();
                $friend->first_name = $friendinput->firstname;
                $friend->last_name = $friendinput->lastname;
                $friend->payminder_id = $payminder->id;
                $friend->phonenumber = $friendinput->phone;
                $friend->amount = $friendinput->amount;
                $friend->save();

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

                $message = "Beste " . $friend->first_name . ",\n\n" . $payminder->sender_name . " heeft geld voorgeschoten" . $msg . $reknr . ". Heb jij al betaald? Klik hier: api.payminder.nl/c/" . $friend->id . " \n\nNog geen tijd gehad? Geen probleem, ik stuur je morgen weer een berichtje.\n\nGroeten, Bill Cashback\n\nOok je vrienden herinneren aan betalingen?\nDownload Payminder: bit.ly/10ZNepH";

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
            }
        } else {
            //echo "Authentication failure: ". $ret[0];
        }

		return base64_encode($payminder->hash);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /payminders/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function get($hash)
	{
        $dbhash = base64_decode($hash);
		return Payminder::where('hash', '=', $dbhash)->first();
	}

    /**
     * Return the friends array for a specific payminder
     * GET /v1/get/{hash}/friends
     *
     * @param string $hash
     * @return JSON
     */
    public function getFriends($hash)
    {
        $dbhash = base64_decode($hash);

        $payminder = Payminder::where('hash','=',$dbhash)->first();
        return Friend::where('payminder_id','=',$payminder->id)->get();
    }

    public function test($id)
    {
        $friend = Friend::find($id);
        return $friend->number();
    }

    public function show($hash)
    {
        $payminder = Payminder::where('hash', '=',base64_decode($hash))->first();
        $friends = Friend::where('payminder_id', '=', $payminder->id)->get();

        return View::make('show')->with(['payminder' => $payminder, 'friends' => $friends]);
    }
}
