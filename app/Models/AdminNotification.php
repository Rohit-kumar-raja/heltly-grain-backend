<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'reference_id',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Create a notification for a new order.
     */
    public static function orderPlaced(Order $order, string $customerName): self
    {
        return self::create([
            'type' => 'order',
            'title' => 'New Order Received',
            'message' => "Order #{$order->order_number} — ₹" . number_format($order->total) . " by {$customerName}",
            'icon' => 'pi pi-shopping-cart',
            'color' => 'bg-emerald-500',
            'link' => 'orders.index',
            'reference_id' => $order->id,
        ]);
    }

    /**
     * Create a notification for a new user registration.
     */
    public static function newUserRegistered(AppUser $user): self
    {
        return self::create([
            'type' => 'user',
            'title' => 'New User Registered',
            'message' => "{$user->name} joined the platform",
            'icon' => 'pi pi-user-plus',
            'color' => 'bg-blue-500',
            'link' => 'app-users.index',
            'reference_id' => $user->id,
        ]);
    }

    /**
     * Order cancelled notification.
     */
    public static function orderCancelled(Order $order, string $customerName): self
    {
        return self::create([
            'type' => 'order',
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} cancelled by {$customerName}",
            'icon' => 'pi pi-times-circle',
            'color' => 'bg-red-500',
            'link' => 'orders.index',
            'reference_id' => $order->id,
        ]);
    }
}
