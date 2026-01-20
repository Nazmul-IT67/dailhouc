<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PushNotification;
use App\Notifications\DatabaseNotifications\ChatMessageNotification;
use App\Notifications\DatabaseNotifications\FavoriteNotification;
use App\Notifications\DatabaseNotifications\PropertyListingNotification;
use App\Notifications\DatabaseNotifications\MatchNotification;

class NotificationService
{
    /**
     * Send chat message notification
     */
    public static function sendChatNotification($sender, $recipient, $message, $isFile = false, $conversationId = null)
    {
        // Check if recipient has notifications enabled
        if (!$recipient->notifications_enabled) {
            return;
        }
        
        $messageType = $isFile ? 'sent you a file' : 'sent you a message';
        $messageBody = $isFile ? 'Tap to view' : $message;
        
        // Truncate message body if too long for push notification
        $truncatedBody = $messageBody;
        if (strlen($truncatedBody) > 100) {
            $truncatedBody = substr($truncatedBody, 0, 97) . '...';
        }
        
        // Send database notification
        $recipient->notify(new ChatMessageNotification($sender, $messageBody, $conversationId));
        
        // Send push notification if device token exists
        if ($recipient->device_token) {
            $recipient->notify(new PushNotification(
                $sender->name . ' ' . $messageType,
                $truncatedBody
            ));
        }
    }

    /**
     * Send favorite notification
     */
    public static function sendFavoriteNotification($favoriter, $favoritedUser)
    {
        // Check if user has notifications enabled
        if (!$favoritedUser->notifications_enabled) {
            return;
        }
        
        // Send database notification
        $favoritedUser->notify(new FavoriteNotification($favoriter));
        
        // Send push notification if device token exists
        if ($favoritedUser->device_token) {
            $favoritedUser->notify(new PushNotification(
                $favoriter->name . ' added you to favorites!',
                'Someone is interested in your profile. Tap to view!'
            ));
        }
    }

    /**
     * Send property listing notification to interested users
     */
    public static function sendPropertyListingNotification($listing, $creator)
    {
        // Find users with matching preferences and notifications enabled
        $interestedUsers = User::where('identity', 'buyer')
            ->where('preferred_property_type', $listing->property_type)
            ->where('notifications_enabled', true)
            ->where('id', '!=', $creator->id)
            ->limit(10) // Limit to avoid spam
            ->get();

        foreach ($interestedUsers as $interestedUser) {
            // Send database notification
            $interestedUser->notify(new PropertyListingNotification($listing, $creator));
            
            // Send push notification if device token exists
            if ($interestedUser->device_token) {
                $interestedUser->notify(new PushNotification(
                    'New ' . ucfirst($listing->property_type) . ' Available!',
                    $listing->title . ' in ' . ($listing->location ?? 'your area')
                ));
            }
        }
    }

    /**
     * Send match notification when two users match
     */
    public static function sendMatchNotification($user1, $user2)
    {
        // Send database notifications only if users have notifications enabled
        if ($user1->notifications_enabled) {
            $user1->notify(new MatchNotification($user2));
            
            // Send push notification if device token exists
            if ($user1->device_token) {
                $user1->notify(new PushNotification(
                    'New Match! 🎉',
                    'You matched with ' . $user2->name . '! Start a conversation now.'
                ));
            }
        }

        if ($user2->notifications_enabled) {
            $user2->notify(new MatchNotification($user1));
            
            // Send push notification if device token exists
            if ($user2->device_token) {
                $user2->notify(new PushNotification(
                    'New Match! 🎉',
                    'You matched with ' . $user1->name . '! Start a conversation now.'
                ));
            }
        }
    }

    /**
     * Send profile view notification
     */
    public static function sendProfileViewNotification($viewer, $viewedUser)
    {
        if (!$viewedUser->device_token) {
            return;
        }

        $viewedUser->notify(new PushNotification(
            $viewer->name . ' viewed your profile',
            'Someone checked out your profile. Tap to see who!'
        ));
    }

    /**
     * Send welcome notification for new users
     */
    public static function sendWelcomeNotification($user)
    {
        // Check if user has notifications enabled
        if (!$user->notifications_enabled) {
            return;
        }
        
        if (!$user->device_token) {
            return;
        }

        $user->notify(new PushNotification(
            'Welcome to Vetsin! 👋',
            'Complete your profile to start matching with amazing people!'
        ));
    }

    /**
     * Send reminder notification for incomplete profiles
     */
    public static function sendProfileCompletionReminder($user)
    {
        // Check if user has notifications enabled
        if (!$user->notifications_enabled) {
            return;
        }
        
        if (!$user->device_token || $user->is_profile_complete) {
            return;
        }

        $user->notify(new PushNotification(
            'Complete Your Profile 📝',
            'Add more details to your profile to get better matches!'
        ));
    }

    /**
     * Send daily digest notification
     */
    public static function sendDailyDigest($user, $newMatches, $newMessages, $newFavorites)
    {
        // Check if user has notifications enabled
        if (!$user->notifications_enabled) {
            return;
        }
        
        if (!$user->device_token) {
            return;
        }

        $title = 'Your Daily Summary 📊';
        $body = '';

        if ($newMatches > 0) {
            $body .= "You have $newMatches new matches. ";
        }
        if ($newMessages > 0) {
            $body .= "You have $newMessages unread messages. ";
        }
        if ($newFavorites > 0) {
            $body .= "$newFavorites people added you to favorites. ";
        }

        if (empty($body)) {
            $body = 'No new activity today. Keep exploring!';
        }

        $user->notify(new PushNotification($title, $body));
    }
} 