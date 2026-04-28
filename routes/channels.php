<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('mess.{messId}.notices', function ($user, $messId) {
    return $user->getMembershipIn((int) $messId) !== null;
});

// Suggestions chat — mess owner or super admin
Broadcast::channel('mess-suggestions.{messId}', function ($user, $messId) {
    return $user->is_super_admin || $user->isOwnerOf((int) $messId);
});

// Admin-wide popup notifications — super admin only
Broadcast::channel('admin-suggestions', function ($user) {
    return $user->is_super_admin;
});
