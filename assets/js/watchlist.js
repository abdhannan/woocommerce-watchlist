jQuery(document).ready(function ($) {
  // ———————————————
  // 1. Add to Watchlist
  // ———————————————
  $('.add-to-watchlist').on('click', function (e) {
    const $btn = $(this);

    if ($btn.hasClass('not-logged-in')) {
      // User belum login, biarkan link jalan normal
      return;
    }

    e.preventDefault(); // hanya cegah default untuk user login
    var originalText = $btn.text();
    var product_id = $btn.data('product-id');

    $btn.prop('disabled', true).text('Memuat...');
    $btn.siblings('.wl-message').remove();

    $.post(
      wl_ajax.ajax_url,
      {
        action: 'add_to_watchlist',
        security: wl_ajax.nonce,
        product_id: product_id,
      },
      function (response) {
        if (response.success) {
          $btn.text('✔️ Sudah di Watchlist').prop('disabled', true);
        } else {
          $btn.after(
            '<div class="wl-message">⚠️ ' +
              (response.data.message || 'Gagal menambahkan.') +
              '</div>'
          );
          $btn.prop('disabled', false).text(originalText);
        }
      }
    ).fail(function () {
      $btn.after(
        '<div class="wl-message">❌ Terjadi kesalahan. Silakan coba lagi.</div>'
      );
      $btn.prop('disabled', false).text(originalText);
    });
  });

  // ———————————————
  // 2. Remove from Watchlist
  // ———————————————
  $('.remove-from-watchlist').on('click', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const productId = $btn.data('product-id');
    const $item = $btn.closest('.wl-item');

    $btn.prop('disabled', true).text('Menghapus…');
    $btn.siblings('.wl-message').remove();

    $.post(
      wl_ajax.ajax_url,
      {
        action: 'remove_from_watchlist',
        security: wl_ajax.nonce,
        product_id: productId,
      },
      function (res) {
        if (res.success) {
          $item.slideUp(300, function () {
            $(this).remove();
            updateTotals();
          });
        } else {
          $btn.after(
            '<div class="wl-message">⚠️ ' +
              (res.data.message || 'Gagal menghapus.') +
              '</div>'
          );
          $btn.prop('disabled', false).text('❌ Hapus');
        }
      }
    ).fail(function () {
      $btn.after(
        '<div class="wl-message">❌ Terjadi kesalahan. Silakan coba lagi.</div>'
      );
      $btn.prop('disabled', false).text('❌ Hapus');
    });
  });

  // UPDATE TOTALS

  function updateTotals() {
    let totalPrice = 0;
    let totalSaving = 0;

    $('.wl-item').each(function () {
      const $priceBlock = $(this).find('.wl-price');
      let regularPrice = 0;
      let salePrice = 0;

      const $del = $priceBlock.find('del');
      if ($del.length) {
        regularPrice = parseFloat(
          $del
            .text()
            .replace(/[^0-9,]/g, '')
            .replace(',', '.')
        );
        salePrice = parseFloat(
          $priceBlock
            .find('strong')
            .text()
            .replace(/[^0-9,]/g, '')
            .replace(',', '.')
        );
      } else {
        regularPrice = parseFloat(
          $priceBlock
            .find('strong')
            .text()
            .replace(/[^0-9,]/g, '')
            .replace(',', '.')
        );
        salePrice = regularPrice;
      }

      totalPrice += salePrice;
      totalSaving += regularPrice - salePrice;
    });

    $('#wl-total-price').text(formatPrice(totalPrice));
    $('#wl-total-saving').text(formatPrice(totalSaving));
  }

  function formatPrice(price) {
    return 'Rp' + price.toLocaleString('id-ID', { minimumFractionDigits: 0 });
  }

  // ———————————————
  // 3. Print & PDF buttons
  // ———————————————
  $('#wl-print').on('click', function () {
    var printContents = document.getElementById('wl-print-area').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // reload supaya semua event js hidup lagi
  });

  $('#wl-export-pdf').on('click', function () {
    const element = document.getElementById('wl-print-area');

    // Clone area
    const clone = element.cloneNode(true);
    clone.id = 'wc-pdf-wrapper';

    const opt = {
      margin: 0,
      filename: 'watchlist.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, scrollY: 0 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    };

    html2pdf().set(opt).from(clone).save();
  });
});
