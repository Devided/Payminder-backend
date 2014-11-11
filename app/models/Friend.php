<?php

class Friend extends \Eloquent {
	protected $fillable = [];

    public function payminder(){
        return $this->belongsTo('Payminder');
    }

    public function number()
    {
        $number = preg_replace('/[^0-9]+/', '', $this->phonenumber);

        if($number[0] == "0" && $number[1] == "0")
        {
            $number = substr($number, 2);
        } else if($number[0] == "0" && $number[1] == "6")
        {
            $number = substr($number, 1);
            $number = "31" . $number;
        }

        return $number;
    }

    public function paid()
    {
        if($this->paid == 1)
        {
            return true;
        }
        elseif($this->paid == 0)
        {
            return false;
        }

    }
}