<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{code}', function ($user, $code) {
    $conversation = Conversation::findByCode($code);
    
    if (!$conversation) {
        return false;
    }
    
    return $conversation->canAccess($user->id);
});
