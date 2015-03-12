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

        // run this function again in 10sec

        sleep(10);

        Queue::push(function($job) {
            SmsController::checkMessages();
        });

        return;
    }

    public static function recieveMessage($from, $body)
    {
        $number = substr($from, 0, 11);
        echo "got message from $number: $body";

        $mes = new Message();
        $mes->number = $number;
        $mes->text = $body;

        //check message for 'ja'

        $check = substr($body, 0, 5); // first 5 chars
        $check = strtolower($check); // to lowercase

        if(strpos($check, 'ja') !== FALSE) // check if string contains 'ja'
        {
            $mes->detectedPaid = true;
        } else {
            $mes->detectedPaid = false;
        }

        $mes->save();

        // set paid if yes is detected

        if(!$mes->detectedPaid)
        {
            return;
        }

        // huidige tijd - 7*24 uur is waar we in moeten zoeken
        
        $query = time() - 7 * 24 * 60 * 60;
        $reminders = Reminder::where('number', '=', $number)->where('epoch', '>', $query)->orderBy('epoch', 'desc')->first();

        $paycheck = Friend::find($reminders->friend_id);
        $paycheck->paid = true;
        $paycheck->save();

        // delete reminder, so other reminders can be accepted aswell..
        $reminders->delete();
    }
}