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
        $payminder->sender_name = DB::getPdo()->quote($input->sender);
        $payminder->sender_iban = DB::getPdo()->quote($input->iban);
        $payminder->start_time = intval($input->startTime);
        $payminder->end_time = intval($input->sendTime);
        $payminder->ip_address = Request::getClientIp();
        $payminder->description = DB::getPdo()->quote($input->description_p);
        $payminder->save();

        $payminder->hash = Hash::make($payminder->id);
        $payminder->save();

        foreach($input->personList as $friendinput){
            $friend = new Friend();
            $friend->first_name = DB::getPdo()->quote($friendinput->firstname);
            $friend->last_name = DB::getPdo()->quote($friendinput->lastname);
            $friend->payminder_id = $payminder->id;
            $friend->save();
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
		return $hash;
	}

}