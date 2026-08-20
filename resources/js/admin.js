import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import {
    createIcons,
    AlertCircle,
    ArrowDown,
    ArrowLeft,
    ArrowUp,
    BookOpen,
    BookUser,
    CheckCircle2,
    ClipboardList,
    GraduationCap,
    Layers,
    LayoutDashboard,
    LogOut,
    Menu,
    Plus,
    UserPlus,
    Users,
    X,
} from 'lucide';

/**
 * Entry point khusus Panel Admin (terpisah dari resources/js/app.js yang
 * dipakai halaman publik) — supaya Chart.js & Lucide (dipakai untuk
 * dashboard admin yang baru) tidak ikut ter-bundle & dimuat di halaman
 * publik yang tidak membutuhkannya. Cuma import ikon yang benar-benar
 * dipakai (bukan seluruh set) supaya bundle-nya tidak membengkak — tambah
 * di sini kalau ada `data-lucide="..."` baru yang dipakai di Blade.
 */
window.Alpine = Alpine;
window.Chart = Chart;

const ADMIN_ICONS = {
    'alert-circle': AlertCircle,
    'arrow-down': ArrowDown,
    'arrow-left': ArrowLeft,
    'arrow-up': ArrowUp,
    'book-open': BookOpen,
    'book-user': BookUser,
    'check-circle-2': CheckCircle2,
    'clipboard-list': ClipboardList,
    'graduation-cap': GraduationCap,
    layers: Layers,
    'layout-dashboard': LayoutDashboard,
    'log-out': LogOut,
    menu: Menu,
    plus: Plus,
    'user-plus': UserPlus,
    users: Users,
    x: X,
};

Alpine.start();

function renderIcons() {
    createIcons({ icons: ADMIN_ICONS });
}

window.renderAdminIcons = renderIcons;

document.addEventListener('DOMContentLoaded', renderIcons);
