<?php
/**
 * Created by PhpStorm.
 * User: Duco
 * Date: 09-11-14
 * Time: 14:08
 */

class FriendsController extends \BaseController {

    public function setPaid($id)
    {
        // mark friend as payed
        $friend = Friend::find($id);
	
	// get payminder
	$payminder = Payminder::find($friend->payminder_id);

	if($friend->paid == false){
	        // send push notification to origin sender
       		$deviceToken = $payminder->pushID;
		$alert = "";
		if($friend->amount == "0" && $payminder->description == ""){
	        	$alert = $friend->first_name . ' heeft betaald!';
		} else if($friend->amount != "0" && $payminder->description == ""){
			$alert = $friend->first_name . ' heeft €' . $friend->amount . ' betaald!';
		} else if($friend->amount == "0" && $payminder->description != ""){
			$alert = $friend->first_name . ' heeft betaald voor lijstje "' . $payminder->description . '"!';
		} else {
			$alert = $friend->first_name . ' heeft €' . $friend->amount . ' betaald voor lijstje "' . $payminder->description . '"!';
		}

        	$body = [];
        	$body['aps'] = ['alert' => $alert, 'sound' => 'default'];
		//$body['aps']['badge'] = 1;		

        	$cert = '/home/forge/api.payminder.nl/app/controllers/pushcertdev.pem';
		$cert = '/home/forge/api.payminder.nl/app/controllers/pushcertprod.pem';

        	$url = 'ssl://gateway.sandbox.push.apple.com:2195';
        	$url = 'ssl://gateway.push.apple.com:2195';

        	$context = stream_context_create();
        	stream_context_set_option( $context, 'ssl', 'local_cert', $cert );
        	$fp = stream_socket_client( $url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $context );

        	$payload = json_encode( $body );
        	$message = chr( 0 ) . pack( 'n', 32 ) . pack( 'H*', $deviceToken ) . pack( 'n', strlen($payload ) ) . $payload;

        	fwrite( $fp, $message );
        	fclose( $fp );

		    $friend->paid = true;
		    $friend->save();
        }

        return View::make('paid')->with(['id' => $id]);
    }

    public function setNotPaid($id)
    {
        // mark friend as payed
        $friend = Friend::find($id);
        $friend->paid = 0;
        $friend->save();

        return View::make('notpaid')->with(['id' => $id]);
    }
}
