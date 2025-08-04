(function ($) {
  "use strict";

  // Initialize tracking functionality
  function initOrderTracking() {
    const trackingForm = $("#order-tracking-form");
    const trackingInput = $("#invoice_number");
    const trackingBtn = trackingForm.find('button[type="submit"]');
    const trackingResults = $(".tracking-results");
    const trackingMessages = $(".tracking-messages");

    if (trackingForm.length === 0) return;

    // Handle form submission
    trackingForm.on("submit", function (e) {
      e.preventDefault();

      const invoice = trackingInput.val().trim();

      if (!invoice) {
        showMessage("error", "Mohon masukkan nomor invoice");
        return;
      }

      // Show loading state
      const btnText = trackingBtn.find(".btn-text");
      const btnLoading = trackingBtn.find(".btn-loading");

      trackingBtn.prop("disabled", true);
      btnText.hide();
      btnLoading.show();

      trackingResults.hide();
      clearMessages();

      // Get nonce from form
      const nonce = trackingForm.find("#track_order_nonce_field").val();

      // Make AJAX request
      $.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: {
          action: "track_order",
          invoice_number: invoice,
          track_order_nonce_field: nonce,
        },
        success: function (response) {
          if (response.success) {
            displayTrackingResults(response.data);
            showMessage("success", "Pesanan ditemukan");
          } else {
            showMessage(
              "error",
              response.data && response.data.message
                ? response.data.message
                : "Pesanan tidak ditemukan"
            );
            trackingResults.hide();
          }
        },
        error: function () {
          showMessage("error", "Terjadi kesalahan saat mencari pesanan");
          trackingResults.hide();
        },
        complete: function () {
          trackingBtn.prop("disabled", false);
          btnText.show();
          btnLoading.hide();
        },
      });
    });

    // Handle enter key in input
    trackingInput.on("keypress", function (e) {
      if (e.which === 13) {
        trackingForm.submit();
      }
    });

    // Format input to uppercase
    trackingInput.on("input", function () {
      $(this).val($(this).val().toUpperCase());
    });
  }

  // Display tracking results
  function displayTrackingResults(data) {
    const trackingResults = $(".tracking-results");

    // Update order info
    updateOrderInfo(data);

    // Update timeline
    updateTimeline(data);

    // Update delivery info
    updateDeliveryInfo(data);

    // Update proof delivery
    updateProofDelivery(data);

    // Show results
    trackingResults.show();
  }

  // Update order information
  function updateOrderInfo(data) {
    $(".invoice-value").text(data.invoice || "-");
    $(".status-badge")
      .removeClass("pending processing shipped delivered cancelled")
      .addClass(data.status || "pending")
      .text(data.status_label || "Pending");
  }

  // Update timeline
  function updateTimeline(data) {
    const timelineContent = $(".timeline-content");

    if (data.timeline_html) {
      timelineContent.html(data.timeline_html);
    } else {
      timelineContent.html(
        '<p class="no-timeline">Belum ada data timeline</p>'
      );
    }
  }

  // Update delivery info
  function updateDeliveryInfo(data) {
    if (
      data.delivery_info &&
      (data.delivery_info.driver_name || data.delivery_info.driver_phone)
    ) {
      $(".driver-name").text(data.delivery_info.driver_name || "-");
      $(".phone-number").text(data.delivery_info.driver_phone || "-");
      $(".delivery-info").show();
    } else {
      $(".delivery-info").hide();
    }
  }

  // Update proof delivery
  function updateProofDelivery(data) {
    const proofSection = $(".proof-delivery");
    const proofImage = $(".proof-image");

    if (data.proof_image && data.proof_image !== "") {
      proofImage.html(
        '<img src="' + data.proof_image + '" alt="Bukti Pengiriman" />'
      );
      proofSection.show();
    } else {
      proofSection.hide();
    }
  }

  // Show message
  function showMessage(type, message) {
    const messageClass =
      type === "success" ? "success-message" : "error-message";
    const messageElement = $(".tracking-messages ." + messageClass);

    // Clear previous messages
    $(
      ".tracking-messages .success-message, .tracking-messages .error-message"
    ).hide();

    if (messageElement.length > 0) {
      messageElement.text(message).show();
    } else {
      const messageHtml =
        '<div class="' + messageClass + '">' + message + "</div>";
      $(".tracking-messages").html(messageHtml);
    }
  }

  // Clear messages
  function clearMessages() {
    $(
      ".tracking-messages .success-message, .tracking-messages .error-message"
    ).hide();
  }

  // Initialize when document is ready
  $(document).ready(function () {
    initOrderTracking();
  });
})(jQuery);
