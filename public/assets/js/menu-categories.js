/**
 * CuanFlow Dashboard Drawers
 * ───────────────────────────────────────────────────────────
 * Menangani pembukaan dan penutupan drawer/modal kategori.
 */
(function () {
  'use strict';

  var activeFolder = null;

  /**
   * Membuka drawer folder.
   */
  window.openFolder = function (slug) {
    var overlay = document.getElementById('folder-' + slug);
    if (!overlay) return;

    // Tutup yang lama jika ada
    if (activeFolder && activeFolder !== slug) {
      closeFolderImmediate(activeFolder);
    }

    activeFolder = slug;

    // Tampilkan elemen container
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Lock scrolling

    // Trigger animasi setelah browser merender display:flex
    requestAnimationFrame(function () {
      overlay.classList.add('folder-active');
    });
  };

  /**
   * Menutup drawer folder dengan animasi.
   */
  window.closeFolder = function (slug) {
    var overlay = document.getElementById('folder-' + slug);
    if (!overlay) return;

    overlay.classList.remove('folder-active');
    
    // Tunggu animasi selesai (300ms sesuai CSS) sebelum hidden
    setTimeout(function () {
      if (!overlay.classList.contains('folder-active')) {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        if (activeFolder === slug) activeFolder = null;
      }
    }, 300);
  };

  /**
   * Menutup instan tanpa animasi.
   */
  function closeFolderImmediate(slug) {
    var overlay = document.getElementById('folder-' + slug);
    if (!overlay) return;
    overlay.classList.remove('folder-active');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
    if (activeFolder === slug) activeFolder = null;
  }

  /**
   * Klik di luar drawer untuk menutup.
   */
  window.closeFolderOnBackdrop = function (event, slug) {
    // Jika target klik adalah backdrop (elemen overlay itu sendiri)
    if (event.target === event.currentTarget || event.target.classList.contains('folder-backdrop')) {
      closeFolder(slug);
    }
  };

  /**
   * Shortcut ESC untuk menutup.
   */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && activeFolder) {
      closeFolder(activeFolder);
    }
  });

})();
