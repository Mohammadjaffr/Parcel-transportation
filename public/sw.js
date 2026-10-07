const CACHE_NAME = 'morsal-cache-v1';
const OFFLINE_URL = '/offline.html';

// حفظ صفحة الأوفلاين في الكاش عند التثبيت
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([
                OFFLINE_URL,
                '/assets/image/icon-192.png'
            ]);
        })
    );
    self.skipWaiting();
});

// تنظيف الكاش القديم عند التحديث
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => clients.claim())
    );
});

// محاولة جلب الطلب من السيرفر، وإذا تعذر وكان طلباً لصفحة ويب، يتم إرجاع صفحة الأوفلاين
self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(async () => {
                const cache = await caches.open(CACHE_NAME);
                return await cache.match(OFFLINE_URL);
            })
        );
    } else {
        // باقي الطلبات (صور، خطوط، سكربتات) تمر بشكل طبيعي للسيرفر
        event.respondWith(fetch(event.request));
    }
});