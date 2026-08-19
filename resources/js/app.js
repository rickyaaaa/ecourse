import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Reveal-on-scroll ringan untuk halaman publik (marketing). Elemen dengan
 * class `.reveal` TETAP terlihat penuh secara default (lihat aturan
 * `.reveal` di app.css) — class `js-ready` di <html> baru ditambahkan di
 * sini, dan baru saat itu CSS menyembunyikan elemen sebelum di-reveal.
 * Jadi kalau skrip ini gagal jalan sama sekali, konten tetap tampil utuh,
 * bukan hilang. Elemen yang sudah kelihatan saat observer dipasang langsung
 * di-reveal tanpa jeda (tidak menunggu event scroll).
 */
document.documentElement.classList.add('js-ready');

if (! window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
    );

    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
} else {
    // Reduced-motion atau browser tanpa IntersectionObserver: tampilkan langsung.
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
}
