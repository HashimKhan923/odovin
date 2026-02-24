<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\ServiceBooking;

class ProviderNotificationService
{
    // ── Notify PROVIDER ──────────────────────────────────────────────────────

    public static function newBooking(ServiceBooking $booking): void
    {
        $provider = $booking->serviceProvider;
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'            => $provider->user_id,
            'service_booking_id' => $booking->id,
            'type'               => 'booking',
            'title'              => 'New Booking Request',
            'message'            => "{$booking->user->name} booked {$booking->service_type} for "
                                    . $booking->scheduled_date->format('M d, Y \a\t H:i'),
            'action_url'         => route('provider.bookings.show', $booking),
            'priority'           => 'warning',
        ]);
    }

    public static function bookingCancelled(ServiceBooking $booking): void
    {
        $provider = $booking->serviceProvider;
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'            => $provider->user_id,
            'service_booking_id' => $booking->id,
            'type'               => 'booking',
            'title'              => 'Booking Cancelled by Customer',
            'message'            => "{$booking->user->name} cancelled their {$booking->service_type} appointment"
                                    . " scheduled for " . $booking->scheduled_date->format('M d, Y'),
            'action_url'         => route('provider.bookings.show', $booking),
            'priority'           => 'critical',
        ]);
    }

    public static function newReview(ServiceBooking $booking): void
    {
        $provider = $booking->serviceProvider;
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'            => $provider->user_id,
            'service_booking_id' => $booking->id,
            'type'               => 'booking',
            'title'              => "New {$booking->rating}★ Review",
            'message'            => "{$booking->user->name} rated your {$booking->service_type} service.",
            'action_url'         => route('provider.bookings.show', $booking),
            'priority'           => 'info',
        ]);
    }

    public static function upcomingReminder(ServiceBooking $booking): void
    {
        $provider = $booking->serviceProvider;
        if (!$provider?->user_id) return;

        Alert::create([
            'user_id'            => $provider->user_id,
            'service_booking_id' => $booking->id,
            'type'               => 'booking',
            'title'              => 'Upcoming Appointment Tomorrow',
            'message'            => "{$booking->service_type} for {$booking->user->name} is scheduled tomorrow at "
                                    . $booking->scheduled_date->format('H:i'),
            'action_url'         => route('provider.bookings.show', $booking),
            'priority'           => 'info',
        ]);
    }

    // ── Notify USER ──────────────────────────────────────────────────────────

    public static function statusUpdated(ServiceBooking $booking, string $newStatus): void
    {
        if (!$booking->user_id) return;

        $config = match ($newStatus) {
            'confirmed'   => [
                'title'    => 'Booking Confirmed ✓',
                'message'  => "Your {$booking->service_type} appointment on "
                              . $booking->scheduled_date->format('M d, Y \a\t H:i')
                              . " has been confirmed by {$booking->serviceProvider->name}.",
                'priority' => 'info',
            ],
            'in_progress' => [
                'title'    => 'Service In Progress',
                'message'  => "{$booking->serviceProvider->name} has started working on your "
                              . "{$booking->service_type}. We'll notify you when it's done.",
                'priority' => 'info',
            ],
            'completed'   => [
                'title'    => 'Service Completed ✓',
                'message'  => "Your {$booking->service_type} has been completed by "
                              . "{$booking->serviceProvider->name}."
                              . ($booking->final_cost ? " Final cost: \${$booking->final_cost}." : '')
                              . " Please leave a review!",
                'priority' => 'info',
            ],
            'cancelled'   => [
                'title'    => 'Booking Cancelled by Provider',
                'message'  => "Unfortunately, {$booking->serviceProvider->name} has cancelled your "
                              . "{$booking->service_type} appointment scheduled for "
                              . $booking->scheduled_date->format('M d, Y') . ".",
                'priority' => 'critical',
            ],
            default => null,
        };

        // No notification for 'pending' or unrecognised statuses
        if (!$config) return;

        Alert::create([
            'user_id'            => $booking->user_id,
            'vehicle_id'         => $booking->vehicle_id,
            'service_booking_id' => $booking->id,
            'type'               => 'booking',
            'title'              => $config['title'],
            'message'            => $config['message'],
            'action_url'         => route('bookings.show', $booking),
            'priority'           => $config['priority'],
        ]);
    }
}