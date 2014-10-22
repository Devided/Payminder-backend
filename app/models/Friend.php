<?php

class Friend extends \Eloquent {
	protected $fillable = [];

    public function payminder(){
        return $this->belongsTo('Payminder');
    }
}