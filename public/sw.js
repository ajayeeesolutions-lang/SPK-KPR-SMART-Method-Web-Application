const CACHE_NAME = 'kpr-smart-v1';

self.addEventListener('install', (event) => {
    console.log('[ServiceWorker] Installed');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[ServiceWorker] Activated');
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // PWA membutuhkan event fetch agar browser mengenalinya sebagai aplikasi installable.
    // Karena ini aplikasi dinamis dengan database, kita biarkan fetch berjalan normal tanpa full offline cache
    // agar data selalu real-time dari server/hostingan.
    event.respondWith(fetch(event.request).catch(() => {
        return new Response('Anda sedang offline. Silakan periksa koneksi internet Anda.');
    }));
});
