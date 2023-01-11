jQuery(function ($) {
  $(".set-range-buget").on("change", function () {
    let value = $(this)
      .val()
      .toLocaleString("id-ID", { style: "currency", currency: "IDR" });
    console.log(value);
    $("#rangebuget").html(value);
  });

  $(".changestatus").on("click", function () {
    let dataid = $(this).data("id");
    let status = $(this).data("status");
    let data = {
      action: "update_meta_js",
      id: dataid,
      key: "status",
      val: status,
    };
    jQuery.post(custom.ajaxurl, data, function (response) {
      console.log("Update Berhasil " + response);
    });
  });
  $(".print-invoice").on("click", function () {
  	$('#printme').printThis();
  });

  $("#type").chained("#merk");
  $(".main-carousel").flickity({
    // options
    cellAlign: "left",
    contain: true,
    pageDots: false,
    wrapAround: true,
    autoPlay: 5000,
    // fullscreen: true
  });
  $('[data-bs-toggle="tooltip"]').tooltip();

  $("#bookingform").submit(function (event) {
    $(this).addClass("was-validated");
    setTimeout(function () {
      $("#bookingform").removeClass("was-validated");
    }, 3000);
    const data = {
      action: "submit_order",
      data: $("#bookingform").serializeArray(),
    };
    const tanggal = $('[name="tanggal"]').val();

    if (typeof tanggal === "string" && tanggal.trim().length === 0) {
      console.log("tanggal belum diisi");
      // $('[name="tanggal"]').parent().append('<div class="invalid-tooltip">Tanggal Wajib diisi</div>');
    } else {
      jQuery.post(custom.ajaxurl, data, function (response) {
        // console.log('Got this from the server: ' + response);
        $("#bookingform")[0].reset();
        jQuery.event.trigger({
          type: "keypress",
          which: 27,
        });
      });
    }
    event.preventDefault();
  });
});

// enable GLightbox global
const lightbox = GLightbox();
(function ($) {
  "use strict";
  // on submit whatsapp
  $(document).on("submit", ".form-order", function (e) {
    e.preventDefault();
    let $this = $(this);
    let nama = $this.find('input[name="nama"]').val();
    let email = $this.find('input[name="email"]').val();
    let waktu = $this.find('input[name="waktu"]').val();
    let phonePelanggan = $this.find('input[name="phone-pelanggan"]').val();
    let phone = $this.find('input[name="phone"]').val();
    let namaWeb = $this.find('input[name="nama-web"]').val();

    // validate nama
    if (nama != "") {
      let paragraph =
        "Hallo, %0a Saya " +
        nama +
        ", ingin memesan layanan jasa " +
        namaWeb +
        " pada " +
        waktu +
        ".%0a%0a Terima kasih.";
      let link =
        "https://api.whatsapp.com/send?phone=" + phone + "&text=" + paragraph;
      window.open(link, "_blank");
    } else {
      $(".error").html(
        '<div class="alert alert-danger">Nama harus diisi</div>'
      );
      // set timeout 3 second
      setTimeout(function () {
        $(".error").html("");
      }, 3000);
    }
  });
})(jQuery);
