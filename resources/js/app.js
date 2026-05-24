import './bootstrap';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import Swal from 'sweetalert2';
import { get, set, update } from 'idb-keyval';
import axios from 'axios';

// Replace this with your actual configuration from the Firebase Console
const firebaseConfig = {
  apiKey: "AIzaSyD-3C7VCi5a7Pdwbmb-FFoq-8gVlooFR78",
  authDomain: "tebaslahan-985c8.firebaseapp.com",
  projectId: "tebaslahan-985c8",
  storageBucket: "tebaslahan-985c8.firebasestorage.app",
  messagingSenderId: "192623265386",
  appId: "1:192623265386:web:2b5d14463f430db4ae6c13",
  measurementId: "G-C7X63RN8D9"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

// Inisialisasi Notifikasi
const Toast = Swal.mixin({
    toast: true,
    position: 'top', // Mengubah ke tengah atas
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    showClass: {
        popup: 'animate__animated animate__fadeInDown' // Efek muncul dari atas
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp' // Efek menghilang ke atas
    },
    didOpen: (toast) => {
        // Logika untuk Swipe to Dismiss (Slide kiri/kanan untuk menutup)
        let startX;
        toast.addEventListener('mousedown', (e) => startX = e.clientX);
        toast.addEventListener('touchstart', (e) => startX = e.touches[0].clientX);

        const handleMove = (endX) => {
            const diffX = endX - startX;
            if (Math.abs(diffX) > 100) { // Jika digeser lebih dari 100px
                toast.style.transform = `translateX(${diffX > 0 ? '100%' : '-100%'})`;
                toast.style.opacity = '0';
                setTimeout(() => Swal.close(), 200);
            }
        };

        toast.addEventListener('mouseup', (e) => handleMove(e.clientX));
        toast.addEventListener('touchend', (e) => handleMove(e.changedTouches[0].clientX));

        // Standar Swal: Stop timer saat di-hover
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// MENANGANI PESAN SAAT WEB TERBUKA (Foreground)
onMessage(messaging, async (payload) => {
    const activeNotification = await get('notificationStatus');
    if (!activeNotification) return;
    let isUpdate = false;

    await update('listNotifications', (list) => {
        const currentList = list || [];
        const { slug, timestamp, id, title, body } = payload.data;
        console.log('titleDoang',    title);

        const isDuplicate = currentList.some(item => item.id === id);
        if (isDuplicate) return currentList; // Kembalikan list tanpa perubahan
        
        isUpdate = true;
        currentList.unshift({
            title,
            body,
            slug,
            timestamp,
            id,
            isRead: false
        });
        return currentList;
    });

    if(isUpdate) {
        await update('lengthNotifications', (len)=>{
            const currentLen = parseInt(len) || 0;
            return currentLen + 1;
        })
    }

    Toast.fire({
        icon: 'info',
        title: payload.data.title,
        text: payload.data.body
    });
});

// FOrmat IDR
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

const getSecondsUntilMidnightWIB = () => {
    const now = new Date();

    // Konversi waktu saat ini ke zona waktu Jakarta (WIB)
    const jakartaTimeStr = now.toLocaleString("en-US", {
        timeZone: "Asia/Jakarta"
    });
    const jakartaTime = new Date(jakartaTimeStr);

    // Buat objek waktu untuk jam 12 malam (00:00) besoknya di Jakarta
    const midnightJakarta = new Date(jakartaTime);
    midnightJakarta.setHours(24, 0, 0, 0); // Set ke jam 24:00:00 hari ini (alias 00:00 besok)

    // Hitung selisihnya (dalam milidetik) lalu ubah ke detik
    const diffInSeconds = Math.floor((midnightJakarta.getTime() - jakartaTime.getTime()) / 1000);

    return diffInSeconds;
}

// EKSPOS KE GLOBAL agar bisa dipanggil di Blade
window.firebaseMessaging = messaging;
window.getFirebaseToken = getToken; 
window.Swal = Swal;
window.Toast = Toast; // Sekarang kamu bisa panggil Toast.fire() di mana saja
window.formatRupiah = formatRupiah;
window.getSecondsUntilMidnightWIB = getSecondsUntilMidnightWIB;
window.idb_get = get;
window.idb_set = set;
window.idb_update = update;
window.axios = axios;