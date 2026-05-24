<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\CloudMessage;
// use App\Models\FcmDevice;


class FCMController extends Controller
{
    public function testSend(Request $request)
    {
        $topic = $request->topic;
        $title = $request->title;
        $body = $request->body;
        $url = $request->url;
        try {
            // 1. PROSES KIRIM VIA FCM PUSH NOTIFICATION
            $messaging = app('firebase.messaging');

            $message = CloudMessage::new()->toTopic($topic)
                ->withData([
                    'title' => $title,
                    'body'  => $body,
                    'slug'   => $url,
                    'id'   => 1,
                    'timestamp' => now()->toDateTimeString(),
                    'frontside' => false
                ]);

            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error("FCM Send General & Database Log Error: " . $e->getMessage());
            return false;
        }
    }

    public static function sendNotification(string $topic, string $title, string $body, string $url)
    {
        try {
            // 1. PROSES KIRIM VIA FCM PUSH NOTIFICATION
            $messaging = app('firebase.messaging');
            $safeTopic = str_replace(' ', '_', strtolower($topic));

            Log::info("FCM TOPIC: " , ['safeTopic'=>$safeTopic, 'title'=>$title, 'body'=>$body, 'url'=>$url]);
            $message = CloudMessage::new()->toTopic($safeTopic)
                ->withData([
                    'title' => $title,
                    'body'  => $body,
                    'url'   => $url,
                    'timestamp' => now()->toDateTimeString(),
                    'frontside' => 'false'
                ]);

            $messaging->send($message);

            // 2. LOGIKA PENYIMPANAN KE DATABASE LOG LOKAL
            $now = now();

            if ($safeTopic === 'admin') {
                // JIKA TARGET ADMIN: Ambil semua ID user yang memiliki role admin
                $adminIds = DB::table('users')->where('role', 'admin')->pluck('id');
                
                $bulkNotificationData = [];
                foreach ($adminIds as $adminId) {
                    $bulkNotificationData[] = [
                        'user_id'    => $adminId,
                        'title'      => $title,
                        'body'       => $body,
                        'url'        => $url,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Jalankan Bulk Insert jika data admin ditemukan
                if (!empty($bulkNotificationData)) {
                    DB::table('notifications')->insert($bulkNotificationData);
                }
            } else {
                // JIKA TARGET USER BIASA: Tentukan ID dari parameter atau ekstrak dari nama topik ('user_5' -> 5)
                $userId = $targetUserId ?? str_replace('user_', '', $safeTopic);
                
                if (is_numeric($userId)) {
                    DB::table('notifications')->insert([
                        'user_id'    => $userId,
                        'title'      => $title,
                        'body'       => $body,
                        'url'        => $url,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("FCM Send General & Database Log Error: " . $e->getMessage());
            return false;
        }
    }

    public static function subscribeTopic(string $token, string $topic)
    {
        if (!$token) return false;

        try {
            $messaging = app('firebase.messaging');
            $safeTopic = str_replace(' ', '_', strtolower($topic));
            Log::info('Subscribe Topic: ' , ['safeTopic'=>$safeTopic, 'token'=>$token]);
            $messaging->subscribeToTopic($safeTopic, $token);
            Log::info('Subscribe Topic: berhasil', ['safeTopic'=>$safeTopic, 'token'=>$token]);
            return true;
        } catch (\Exception $e) {
            Log::error("FCM Subscribe Error: " . $e->getMessage());
            return false;
        }
    }

    public static function unsubscribeTopic(string $token, string $topic)
    {
        if (!$token) return false;

        try {
            $messaging = Firebase::messaging();
            $safeTopic = str_replace(' ', '_', strtolower($topic));
            
            $messaging->unsubscribeFromTopic($safeTopic, $token);
            return true;
        } catch (\Exception $e) {
            Log::error("FCM Subscribe Error: " . $e->getMessage());
            return false;
        }
    }

    public function subscribeNotificationTopics(Request $request)
    {
        $token = $request->fcm_token;
        $oldTopics = collect($request->old_topics)->flatten()->map(fn($item) => (string)$item)->toArray();
        $newTopics = collect($request->new_topics)->flatten()->map(fn($item) => (string)$item)->toArray();
        
        $messaging = Firebase::messaging(); // Langsung ambil env firebase otomatis
        
        $toSubscribe = array_diff($newTopics, $oldTopics);
        $toUnsubscribe = array_diff($oldTopics, $newTopics);

        // Jika tidak ada perubahan, langsung return (Skip)
        if (empty($toSubscribe) && empty($toUnsubscribe)) {
            return response()->json(['status' => 'skip', 'message' => 'Tidak ada perubahan topik']);
        }

        try {
            foreach ($toSubscribe as $cityName) {
                // Format nama topik agar aman (kecilkan, hapus spasi)
                $topicName = str_replace(' ', '_', strtolower($cityName));
                Log::info('Subscribe Bang anjing Topic: ', ['topicName' => $topicName, 'token' => $token]);
                $messaging->subscribeToTopic($topicName, $token);
            }

            foreach ($toUnsubscribe as $cityName) {
                // Format nama topik agar aman (kecilkan, hapus spasi)
                $topicName = str_replace(' ', '_', strtolower($cityName));
                $messaging->unsubscribeFromTopic($topicName, $token);
            }

            return response()->json(['status' => 'success', 'message' => 'Topik berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public static function sendNotificationNewProperty(string $city, string $typeProperty, string $slug, int $id) {
        try {
            $messaging = Firebase::messaging();
            $city = str_replace(' ', '_', strtolower($city));
            $typeProperty = $typeProperty;
            $slug = $slug;
            $id = $id;

            $message = CloudMessage::new()->toTopic($city)
                ->withData([
                    'title' => ucfirst($typeProperty) . " Baru",
                    'body' => ucfirst($typeProperty) . ' baru telah ditambahkan di kota ' . $city,
                    'id' => $id,
                    'typeProperty' => strtolower($typeProperty),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'slug' => route('home.property-details', $slug) // URL tujuan
                ]);

            $messaging->send($message);
            return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil dikirim']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);   
        }
    }

    public function toggleNotification(Request $request) {
        $topics = collect($request->topics)->flatten()->map(fn($item) => (string)$item)->toArray();
        $token = $request->fcm_token;
        $isActive = $request->status;
        
        $messaging = Firebase::messaging();
        try {
            foreach ($topics as $topic) {
                $topicName = str_replace(' ', '_', strtolower($topic));
                
                if ($isActive) {
                    $messaging->subscribeToTopic($topicName, $token);
                } else {
                    $messaging->unsubscribeFromTopic($topicName, $token);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil diubah']);
    }
}
