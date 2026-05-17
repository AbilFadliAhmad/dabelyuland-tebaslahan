<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
// use App\Models\FcmDevice;


class FCMController extends Controller
{
    public function subscribeTopics(Request $request)
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
            // FcmDevice::updateOrCreate(
            //     ['token' => $token] // Cari berdasarkan token
            // );

            foreach ($toSubscribe as $cityName) {
                // Format nama topik agar aman (kecilkan, hapus spasi)
                $topicName = str_replace(' ', '_', strtolower($cityName));
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

    public function sendNotification(Request $request) {
        try {
            $messaging = Firebase::messaging();
            $city = str_replace(' ', '_', strtolower($request->city));
            $typeProperty = $request->typeProperty;
            $slug = $request->slug;
            $id = $request->id;

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

                // Untuk sementara kode dikomentar sampai menemukan solusi penggunaan yang sesuai
                // FcmDevice::where('token', $token)->update([
                //     'active' => $isActive
                // ]);

            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil diubah']);
    }
}
