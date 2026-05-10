import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: window.location.hostname, // الأهم: يجبره على الاتصال بسيرفرك (arta.tiyar.cc)
    wsPort: 6001,
    wssPort: 443,
    forceTLS: false, // يمنع المتصفح من محاولة استخدام تشفير معقد يبطئ الاتصال
    disableStats: true, // يمنع إرسال إحصائيات تبطئ التحميل
    enabledTransports: ['ws', 'wss'], // يحدد نوع الاتصال مباشرة بدون تخمين
});
