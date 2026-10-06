self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// إرسال الطلب للسيرفر مباشرة ليعمل شرط isMobile في كل طلب
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});