<?php

class Payminder extends \Eloquent {
	protected $fillable = [];

    public function friends(){
        $this->hasMany('Friend');
    }
}