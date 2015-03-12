<?php

class SmsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sms
	 *
	 * @return Response
	 */
	public static function send($id)
	{
		//
        Log::info("got id: ".$id);
        Friend::sendsms($id);
	}

    public static function checkMessages()
    {
        require '/home/vagrant/Code/PMNDR/vendor/mgp25/whatsapi/src/events/MyEvents.php';

        $username = "31629200257";
        $nickname = "Payminder";
        $password = "Odns/OKOkg3oT9CzrKP47GhlIcc=";
        $debug = true;

        $w = new WhatsProt($username, $nickname, $debug);
        $events = new MyEvents($w);
        $events->setEventsToListenFor($events->activeEvents); //You can also pass in your own array with a list of events to listen too instead.

        //Now continue with your script.
        $w->connect();
        $w->loginWithPassword($password);

        // check for messages
        $w->pollMessage();

        $w->disconnect();

        unset($w);
    }

    public static function recieveMessage($from, $body)
    {
        $number = substr($from, 0, 11);
        echo "got message from $number: $body";

        $mes = new Message();
        $mes->number = $number;
        $mes->text = $body;

        //todo: check message for 'ja' and set paid

        $check = substr($body, 0, 5);
        $check = strtolower($check);

        if(strpos($check, 'ja') !== FALSE)
        {
            $mes->detectedPaid = true;
        } else {
            $mes->detectedPaid = false;
        }

        $mes->save();
    }
}