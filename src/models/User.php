<?php
namespace Pichkrement\Messenger\Models;

use Pichkrement\Messenger\Models\Conversation as Conversation;

class User extends \Eloquent {


	public function messages(){
		return $this->hasMany('Pichkrement\Messenger\Models\Message');
	}

	public function conversations()
    {
        return $this->belongsToMany('Pichkrement\Messenger\Models\Conversation');
    }

    // send functions

    /**
     * send
     *
     * send message to receiver
     *
     * @param Pichkrement\Messenger\Models\User $receiver receiver
     * @return boolean
     */
    public function send($receiver, $text, $subject){

    	//test preconditions
    	if(! get_class($receiver) === 'Pichkrement\Messenger\Models\User')
    		return false;


    	//test if there is an conversation between the two members
    	$con = Conversation::user_filter(array($this, $receiver));

    	//create new conversation
    	if (is_null($con)){
    		$con = new Conversation;
    		$con->name = $subject;
	    	//link users to conversation
	    	$con->save();

	    	$con->users()->sync(array($receiver->id, $this->id));
    	}

    	
    	//create new Message and add it to conversation
    	$msg = new Message;
    	$msg->content = $text;
    	$msg->user_id = $this->id;

    	$con->messages()->save($msg);
    	
    	return true;
    }

}
