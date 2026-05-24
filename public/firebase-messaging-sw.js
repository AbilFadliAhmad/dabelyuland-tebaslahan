// 1. Import SDK Firebase khusus untuk Service Worker (menggunakan versi compat agar lebih stabil di SW)
importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.12.0/firebase-messaging-compat.js');

// IMPORT idb-keyval (Versi UMD agar bisa diakses di SW)
importScripts('https://cdn.jsdelivr.net/npm/idb-keyval@6/dist/umd.js');

// 2. Inisialisasi Firebase di dalam Service Worker
// Gunakan konfigurasi yang SAMA persis dengan yang ada di app.js Anda
firebase.initializeApp({
  apiKey: "AIzaSyD-3C7VCi5a7Pdwbmb-FFoq-8gVlooFR78",
  authDomain: "tebaslahan-985c8.firebaseapp.com",
  projectId: "tebaslahan-985c8",
  storageBucket: "tebaslahan-985c8.firebasestorage.app",
  messagingSenderId: "192623265386",
  appId: "1:192623265386:web:2b5d14463f430db4ae6c13",
  measurementId: "G-C7X63RN8D9"
});

// 3. Ambil instance messaging
const messaging = firebase.messaging();

// 4. Menangani pesan saat aplikasi sedang tertutup (Background)
messaging.onBackgroundMessage(async(payload) => {
  // Ambil fallback dari payload.notification jika payload.data ternyata kosong
  const title = payload.data?.title || payload.notification?.title || "Notifikasi Sistem";
  const body = payload.data?.body || payload.notification?.body || "Ada pembaruan terbaru.";
  const slug = payload.data?.slug || "";
  const id = payload.data?.id || new Date().getTime().toString();
  
  // PERBAIKAN: Konversi string "false" / "true" dari API V1 menjadi Boolean murni secara aman
  const isFrontside = payload.data?.frontside === 'true' || payload.data?.frontside === true || payload.data?.frontside === undefined;
  try {
    // Check apakah notifikasi aktif
    const isNotificationActive = await idbKeyval.get('notificationStatus');
    if (!isNotificationActive && isFrontside) return;

    let isUpdate = false;
    const notificationTitle = title;
    const notificationOptions = {
      body: body,
      icon: '/frontside/img/icon/dabelyuland.png', // Pastikan file icon ini ada di folder public
      data: {
        url: slug,
      }
    };
    
    if (isFrontside) {
      await idbKeyval.update('listNotifications', (list) => {
          const currentList = list || [];
          
          const isDuplicate = currentList.some(item => item.id === id);
          if (isDuplicate) return currentList; // Kembalikan list tanpa perubahan

          isUpdate = true;
          
          currentList.unshift({
              title,
              body,
              slug,
              timestamp: new Date().toLocaleString(),
              id,
              isRead: false
          });
          return currentList;
      });

      if(isUpdate) {
          await idbKeyval.update('lengthNotifications', (len)=>{
              const currentLen = parseInt(len) || 0;
              return currentLen + 1;
          })
      }
    }
    console.log('[SW] Menampilkan notifikasi:', notificationTitle);
    return self.registration.showNotification(notificationTitle, notificationOptions);
  } catch (error) {
    console.error('[SW] Error mengambil status dari IndexedDB:', error);
    return self.registration.showNotification(title, { body: body, icon: '/frontside/img/icon/dabelyuland.png' });  }
});