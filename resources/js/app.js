// Splitty PWA bootstrap

// 1. Register service worker (served from /sw.js)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => console.warn('SW registration failed', err));
    });
}

// 2. Install prompt handling
let deferredInstall = null;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstall = e;
    document.dispatchEvent(new CustomEvent('pwa:installable'));
});

window.installPwa = async () => {
    if (!deferredInstall) return false;
    deferredInstall.prompt();
    const { outcome } = await deferredInstall.userChoice;
    deferredInstall = null;
    return outcome === 'accepted';
};

// 3. Ripple touch feedback for cards
document.addEventListener('click', (e) => {
    const tap = e.target.closest('.tap-anim');
    if (!tap) return;
    tap.animate(
        [{ transform: 'scale(1)' }, { transform: 'scale(.97)' }, { transform: 'scale(1)' }],
        { duration: 180, easing: 'ease-out' }
    );
});

// 4. Locale + RTL toggler (server-side route sets cookie + redirects back)
window.toggleLocale = (locale) => {
    const here = encodeURIComponent(location.pathname + location.search);
    location.assign(`/locale/${locale}?redirect=${here}`);
};

// 5. Voice input (Web Speech API)
window.startVoice = (locale = 'ar-EG') => {
    return new Promise((resolve, reject) => {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SR) return reject(new Error('SpeechRecognition not supported'));
        const r = new SR();
        r.lang = locale;
        r.interimResults = false;
        r.maxAlternatives = 3;
        r.onresult = (e) => resolve([...e.results[0]].map(x => x.transcript));
        r.onerror = (e) => reject(new Error(e.error));
        r.onend = () => {};
        r.start();
    });
};

// 6. Geolocation
window.getLocation = () => new Promise((resolve, reject) => {
    if (!navigator.geolocation) return reject(new Error('Geolocation not supported'));
    navigator.geolocation.getCurrentPosition(
        (p) => resolve({ lat: p.coords.latitude, lng: p.coords.longitude, accuracy: p.coords.accuracy }),
        (e) => reject(e),
        { enableHighAccuracy: true, timeout: 8000 }
    );
});

// 7. Push subscription
window.enablePush = async (vapidPublicKey) => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        throw new Error('Push not supported on this device');
    }
    const reg = await navigator.serviceWorker.ready;
    let permission = await Notification.requestPermission();
    if (permission !== 'granted') throw new Error('Permission denied');
    const sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
    });
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    await fetch('/push/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(sub),
    });
    return true;
};

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
}

// 8. Receipt OCR — uses Tesseract via CDN, lazy-loaded
window.ocrReceipt = async (file) => {
    if (!window.Tesseract) {
        await new Promise((res, rej) => {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
            s.onload = res; s.onerror = rej; document.head.appendChild(s);
        });
    }
    const { data } = await window.Tesseract.recognize(file, 'eng+ara', {
        logger: m => console.debug('[ocr]', m.status, m.progress),
    });
    return parseReceipt(data.text);
};

function parseReceipt(text) {
    const lines = text.split(/\n+/).map(l => l.trim()).filter(Boolean);
    const totals = [];
    const moneyRe = /(\d{1,3}(?:[,.]?\d{3})*(?:[.,]\d{1,2})?)/g;
    for (const line of lines) {
        if (/total|إجمالي|اجمالي|المجموع|الاجمالي/i.test(line)) {
            const m = [...line.matchAll(moneyRe)].map(x => parseFloat(x[1].replace(',', '.')));
            if (m.length) totals.push(Math.max(...m));
        }
    }
    const all = [...text.matchAll(moneyRe)].map(x => parseFloat(x[1].replace(',', '.')));
    const guess = totals[0] ?? Math.max(0, ...all);
    return { amount: guess || null, raw: text, lines };
}
