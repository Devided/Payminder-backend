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

                $reknr = " ";
                if($payminder->sender_iban != "")
                {
                    $reknr = " (".$payminder->sender_iban.")";
                }

                $text = urlencode("Hoi " . $friend->first_name . ", je moet nog " . $friend->amount . " euro betalen aan je vriend " . $payminder->sender_name . ". Al betaald? klik hier: api.payminder.nl/c/".$friend->id." Groetjes, Bill Cashback" . $reknr);
                $to = $friend->number();

                $sess_id = trim($sess[1]); // remove any whitespace
                $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

                // do sendmsg call
                $ret = file($url);
                $send = explode(":",$ret[0]);

                if ($send[0] == "ID") {
                    //echo "successnmessage ID: ". $send[1];
                } else {
                    echo "send message failed";
                }
            }
        } else {
            echo "Authentication failure: ". $ret[0];
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
}
