<?php
/**
 * Created by PhpStorm.
 * User: Duco
 * Date: 20-11-14
 * Time: 13:33
 */

Event::listen('sendSMS', function($id){
    Friend::sendsms($id);

    $date = \Carbon\Carbon::now()->addHours(24);
    Queue::later($date, 'sendSMS', [$id]);
});