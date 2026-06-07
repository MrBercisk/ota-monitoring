/* ============================================================
   OTA MONITOR — navbar.js
   Clock, Breadcrumb, Notification System
   ============================================================ */

/* ── CLOCK ── */
function updateNavbarClock() {
    const now    = new Date();
    const days   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

    const dateEl = document.getElementById('navbar-date');
    const timeEl = document.getElementById('navbar-time');

    if (dateEl) dateEl.textContent =
        days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    if (timeEl) timeEl.textContent =
        now.toTimeString().slice(0, 8);
}

/* ── BREADCRUMB ── */
function updateBreadcrumb() {
    const activeSubItem    = document.querySelector('.menu-sub .menu-item.active > .menu-link');
    const activeParentItem = document.querySelector('.menu-item.active:not(.menu-sub .menu-item) > .menu-link');

    let pageTitle   = '';
    let parentTitle = 'OTA Monitor';

    if (activeSubItem) {
        pageTitle   = activeSubItem.querySelector('div')?.textContent.trim()
                   || activeSubItem.textContent.trim();
        parentTitle = activeParentItem?.querySelector('div')?.textContent.trim()
                   || activeParentItem?.textContent.trim()
                   || 'OTA Monitor';
    } else if (activeParentItem) {
        pageTitle   = activeParentItem.querySelector('div')?.textContent.trim()
                   || activeParentItem.textContent.trim();
        parentTitle = 'OTA Monitor';
    }

    if (!pageTitle) return;

    const titleEl       = document.getElementById('navbar-page-title');
    const breadcrumbEl  = document.getElementById('breadcrumb-current');
    const parentLinkEl  = document.querySelector('#navbar-breadcrumb .breadcrumb-item:first-child a');

    if (titleEl)      titleEl.textContent      = pageTitle;
    if (breadcrumbEl) breadcrumbEl.textContent = pageTitle;
    if (parentLinkEl) parentLinkEl.textContent = parentTitle;
}

/* ── NOTIFICATIONS ── */
const NOTIF_POLL_INTERVAL = 30000; // 30 detik

const NOTIF_ICON_MAP = {
    delay:   { icon: 'ri-flight-takeoff-line', cls: 'delay'   },
    cancel:  { icon: 'ri-close-circle-line',   cls: 'cancel'  },
    ota_low: { icon: 'ri-bar-chart-line',      cls: 'ota_low' },
};

function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60)    return diff + ' dtk lalu';
    if (diff < 3600)  return Math.floor(diff / 60) + ' mnt lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    return Math.floor(diff / 86400) + ' hr lalu';
}

function renderSkeleton() {
    return Array(3).fill(0).map(() => `
        <div class="notif-skeleton">
            <div class="notif-skeleton-icon"></div>
            <div class="notif-skeleton-lines">
                <div class="notif-skeleton-line"></div>
                <div class="notif-skeleton-line short"></div>
            </div>
        </div>
    `).join('');
}

function renderNotifications(data) {
    const list       = document.getElementById('notif-list');
    const badge      = document.getElementById('notif-badge');
    const empty      = document.getElementById('notif-empty');
    const countLabel = document.getElementById('notif-count-label');

    if (!list || !badge || !empty) return;

    // Count label
    if (countLabel) {
        countLabel.textContent = data.notifications?.length > 0
            ? data.notifications.length + ' aktif'
            : '';
    }

    // Badge
    if (data.unread_count > 0) {
        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }

    // Empty state
    if (!data.notifications || data.notifications.length === 0) {
        list.innerHTML     = '';
        empty.style.display = 'block';
        return;
    }

    empty.style.display = 'none';
    list.innerHTML = data.notifications.map(n => {
        const ico = NOTIF_ICON_MAP[n.type] || { icon: 'ri-information-line', cls: 'ota_low' };
        return `
            <div class="notif-item unread">
                <div class="notif-icon ${ico.cls}">
                    <i class="ri ${ico.icon}"></i>
                </div>
                <div class="notif-content">
                    <div class="notif-title">${n.title}</div>
                    <div class="notif-message">${n.message}</div>
                    <div class="notif-time">${timeAgo(n.time)}</div>
                </div>
                <div class="notif-unread-dot"></div>
            </div>
        `;
    }).join('');
}

async function fetchNotifications(showSkeleton = false) {
    const list = document.getElementById('notif-list');
    if (!list) return;

    if (showSkeleton) list.innerHTML = renderSkeleton();

    try {
        const res  = await fetch(window.NOTIF_ROUTE, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        renderNotifications(data);
    } catch (e) {
        console.warn('Gagal fetch notifikasi:', e);
    }
}

/* ── INIT ── */
updateNavbarClock();
setInterval(updateNavbarClock, 1000);

document.addEventListener('DOMContentLoaded', function () {
    updateBreadcrumb();
    fetchNotifications(true);
    setInterval(fetchNotifications, NOTIF_POLL_INTERVAL);

    document.getElementById('notifDropdown')
        ?.addEventListener('show.bs.dropdown', () => fetchNotifications());
});
