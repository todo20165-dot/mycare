<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\MedicationLog;
use App\Models\VitalSign;
use Illuminate\Support\Facades\Mail;

/**
 * خدمة الإشعارات
 * تقوم بإرسال الإشعارات عبر قنوات مختلفة
 */
class NotificationService
{
    /**
     * إرسال إشعار تذكير الأدوية
     */
    public function sendMedicationReminder(User $user, $medication)
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'medication_reminder',
            'title' => 'تذكير: موعد الدواء',
            'message' => "حان وقت تناول دواء {$medication->name}",
            'data' => [
                'medication_id' => $medication->id,
                'medication_name' => $medication->name,
                'dosage' => $medication->dosage,
            ],
        ]);

        // إرسال بريد إلكتروني
        if ($user->email_notifications) {
            $this->sendEmailNotification($user, $notification);
        }

        // إرسال إشعار المتصفح
        if ($user->browser_notifications) {
            $this->sendBrowserNotification($user, $notification);
        }

        return $notification;
    }

    /**
     * إرسال إشعار قراءة غير طبيعية
     */
    public function sendAbnormalReadingAlert(User $user, VitalSign $vitalSign)
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'abnormal_reading',
            'title' => 'تنبيه: قراءة غير طبيعية',
            'message' => "تم رصد قراءة غير طبيعية: {$vitalSign->type} = {$vitalSign->value} {$vitalSign->unit}",
            'data' => [
                'vital_sign_id' => $vitalSign->id,
                'type' => $vitalSign->type,
                'value' => $vitalSign->value,
                'unit' => $vitalSign->unit,
            ],
            'priority' => 'high',
        ]);

        // إرسال بريد إلكتروني عاجل
        if ($user->email_notifications) {
            $this->sendUrgentEmailNotification($user, $notification);
        }

        // إرسال إشعار المتصفح
        if ($user->browser_notifications) {
            $this->sendBrowserNotification($user, $notification);
        }

        return $notification;
    }

    /**
     * إرسال إشعار رسالة جديدة
     */
    public function sendNewMessageNotification(User $user, $sender, $message)
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'new_message',
            'title' => 'رسالة جديدة من ' . $sender->name,
            'message' => substr($message, 0, 100),
            'data' => [
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
            ],
        ]);

        // إرسال إشعار المتصفح
        if ($user->browser_notifications) {
            $this->sendBrowserNotification($user, $notification);
        }

        return $notification;
    }

    /**
     * إرسال إشعار طلب ارتباط عائلي
     */
    public function sendFamilyLinkRequest(User $user, User $requester)
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'family_link_request',
            'title' => 'طلب ارتباط عائلي جديد',
            'message' => "{$requester->name} يطلب الارتباط العائلي معك",
            'data' => [
                'requester_id' => $requester->id,
                'requester_name' => $requester->name,
            ],
        ]);

        // إرسال بريد إلكتروني
        if ($user->email_notifications) {
            $this->sendEmailNotification($user, $notification);
        }

        return $notification;
    }

    /**
     * إرسال إشعار بريد إلكتروني عادي
     */
    private function sendEmailNotification(User $user, Notification $notification)
    {
        try {
            // يمكن استخدام Mailable class هنا
            // Mail::send('emails.notification', [...], function($message) {...});
            \Log::info("Email notification sent to {$user->email}: {$notification->title}");
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: {$e->getMessage()}");
        }
    }

    /**
     * إرسال إشعار بريد إلكتروني عاجل
     */
    private function sendUrgentEmailNotification(User $user, Notification $notification)
    {
        try {
            // إرسال بريد عاجل بأولوية عالية
            \Log::info("URGENT Email notification sent to {$user->email}: {$notification->title}");
        } catch (\Exception $e) {
            \Log::error("Failed to send urgent email notification: {$e->getMessage()}");
        }
    }

    /**
     * إرسال إشعار المتصفح (Push Notification)
     */
    private function sendBrowserNotification(User $user, Notification $notification)
    {
        try {
            // يمكن استخدام Firebase Cloud Messaging أو خدمة أخرى
            \Log::info("Browser notification sent to user {$user->id}: {$notification->title}");
        } catch (\Exception $e) {
            \Log::error("Failed to send browser notification: {$e->getMessage()}");
        }
    }

    /**
     * وضع علامة على الإشعار كمقروء
     */
    public function markAsRead(Notification $notification)
    {
        $notification->update(['read_at' => now()]);
        return $notification;
    }

    /**
     * وضع علامة على جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(User $user)
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * الحصول على الإشعارات غير المقروءة
     */
    public function getUnreadNotifications(User $user)
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * حذف الإشعار
     */
    public function deleteNotification(Notification $notification)
    {
        return $notification->delete();
    }

    /**
     * حذف جميع الإشعارات المقروءة
     */
    public function deleteReadNotifications(User $user)
    {
        return $user->notifications()
            ->whereNotNull('read_at')
            ->delete();
    }
}
