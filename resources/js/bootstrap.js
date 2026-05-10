import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 443, // نستخدم منفذ الموقع نفسه
    wssPort: 443,
    forceTLS: true, // إجبار التشفير ليتوافق مع HTTPS
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});
