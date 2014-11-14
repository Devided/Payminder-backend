<?php
/**
 * Created by PhpStorm.
 * User: Duco
 * Date: 09-11-14
 * Time: 14:08
 */

class FriendsController extends \BaseController {

    public function setPayed($id)
    {
        // mark friend as payed
        $friend = Friend::find($id);
        $friend->paid = true;
        $friend->save();
	
	// get payminder
	$payminder = Payminder::find($friend->payminder_id);

        // send push notification to origin sender
        $deviceToken = $payminder->pushID;
        $alert = $friend->first_name . ' heeft betaald!';

        $body = [];
        $body['aps'] = ['alert' => $alert, 'sound' => 'default'];

        $cert = '/home/forge/api.payminder.nl/app/controllers/pushcertdev.pem';

        $url = 'ssl://gateway.sandbox.push.apple.com:2195';
        //$url = 'ssl://gateway.push.apple.com:2195';

        $context = stream_context_create();
        stream_context_set_option( $context, 'ssl', 'local_cert', $cert );
        $fp = stream_socket_client( $url, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $context );

        $payload = json_encode( $body );
        $message = chr( 0 ) . pack( 'n', 32 ) . pack( 'H*', $deviceToken ) . pack( 'n', strlen($payload ) ) . $payload;

        fwrite( $fp, $message );
        fclose( $fp );

        echo "<head><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"></head>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<center><h2>Super, je hebt betaald!</h2></center>";
    }
}
