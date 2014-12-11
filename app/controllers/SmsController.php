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
        Friend::sendsms($id);
	}
}