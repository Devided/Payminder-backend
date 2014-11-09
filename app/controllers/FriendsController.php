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
        $friend = Friend::find($id);
        $friend->paid = true;
        $friend->save();
        echo "<head><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"></head>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<center><h2>Super, je hebt betaald!</h2></center>";
    }
}