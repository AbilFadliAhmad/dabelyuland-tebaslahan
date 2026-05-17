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
  try {
    // Check apakah notifikasi aktif
    const isNotificationActive = await idbKeyval.get('notificationStatus');
    if (!isNotificationActive) return;

    const { typeProperty, slug, timestamp, id, title, body } = payload.data;
    let isUpdate = false;
    const notificationTitle = title;
    const notificationOptions = {
      body: body,
      icon: 'frontside/img/icon/dabelyuland.png' // Pastikan file icon ini ada di folder public
    };
    
    await idbKeyval.update('listNotifications', (list) => {
        const currentList = list || [];
        
        const isDuplicate = currentList.some(item => item.id === id);
        if (isDuplicate) return currentList; // Kembalikan list tanpa perubahan

        isUpdate = true;
        
        currentList.unshift({
            title,
            body,
            typeProperty,
            slug,
            timestamp,
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
    return self.registration.showNotification(notificationTitle, notificationOptions);
  } catch (error) {
    console.error('[SW] Error mengambil status dari IndexedDB:', error);
  }
});