/**
 * Custom Plugin Meta Box JavaScript
 */

jQuery(document).ready(function ($) {
  "use strict";

  // Initialize meta box functionality
  initMetaBoxes();

  /**
   * Initialize meta box functionality
   */
  function initMetaBoxes() {
    // Format price input
    initPriceFormatting();

    // Status change handling
    initStatusHandling();

    // Timeline functionality
    initTimelineFunctionality();

    // Photo upload functionality
    initPhotoUpload();

    // Form validation
    initFormValidation();
  }

  /**
   * Initialize price formatting
   */
  function initPriceFormatting() {
    var $priceInput = $("#custom_product_harga");

    if ($priceInput.length) {
      // Format price on input
      $priceInput.on("input", function () {
        var value = $(this).val();

        // Remove non-numeric characters except decimal point
        value = value.replace(/[^0-9.]/g, "");

        // Ensure only one decimal point
        var parts = value.split(".");
        if (parts.length > 2) {
          value = parts[0] + "." + parts.slice(1).join("");
        }

        // Limit decimal places to 2
        if (parts[1] && parts[1].length > 2) {
          value = parts[0] + "." + parts[1].substring(0, 2);
        }

        $(this).val(value);
      });

      // Format on blur
      $priceInput.on("blur", function () {
        var value = parseFloat($(this).val());
        if (!isNaN(value)) {
          $(this).val(value.toFixed(2));
        }
      });

      // Add currency symbol before input
      if (!$priceInput.siblings(".price-prefix").length) {
        $priceInput.before('<span class="price-prefix">Rp </span>');
      }
    }

    // Order price formatting
    $('input[name="order_total_price"]').on("blur", function () {
      var value = $(this).val();
      if (value) {
        // Format number with thousands separator
        var formatted = parseFloat(value).toLocaleString("id-ID");
        $(this).val(formatted);
      }
    });

    $('input[name="order_total_price"]').on("focus", function () {
      var value = $(this).val();
      if (value) {
        // Remove formatting for editing
        var clean = value.replace(/[^\d]/g, "");
        $(this).val(clean);
      }
    });
  }

  /**
   * Initialize status handling
   */
  function initStatusHandling() {
    var $statusSelect = $("#custom_order_status, select[name='order_status']");

    if ($statusSelect.length) {
      // Add color coding based on status
      $statusSelect.on("change", function () {
        var status = $(this).val();
        var $option = $(this).find("option:selected");

        // Remove all status classes
        $(this).removeClass(
          "status-pending status-processing status-shipped status-delivered status-cancelled"
        );

        // Add current status class
        $(this).addClass("status-" + status);

        // Show confirmation for certain status changes
        if (status === "cancelled") {
          if (!confirm("Are you sure you want to cancel this order?")) {
            // Revert to previous value
            $(this).val($(this).data("previous-value") || "pending");
            return false;
          }
        }

        // Store current value for potential revert
        $(this).data("previous-value", status);
      });

      // Initialize current status styling
      $statusSelect.trigger("change");
    }
  }

  /**
   * Initialize timeline functionality
   */
  function initTimelineFunctionality() {
    // Timeline date/time input functionality
    $(
      '.timeline-step input[type="date"], .timeline-step input[type="time"]'
    ).on("change", function () {
      var $stepContainer = $(this).closest(".timeline-step");
      var $dateInput = $stepContainer.find('input[type="date"]');
      var $timeInput = $stepContainer.find('input[type="time"]');

      var hasDate = $dateInput.val() !== "";
      var hasTime = $timeInput.val() !== "";

      if (hasDate && hasTime) {
        // Step completed - both date and time filled
        $stepContainer.addClass("completed");
      } else {
        // Step not completed
        $stepContainer.removeClass("completed");
      }

      // Update order status based on timeline progress
      updateOrderStatus();
    });

    // Initialize timeline state on page load
    $(".timeline-step").each(function () {
      var $stepContainer = $(this);
      var $dateInput = $stepContainer.find('input[type="date"]');
      var $timeInput = $stepContainer.find('input[type="time"]');

      var hasDate = $dateInput.val() !== "";
      var hasTime = $timeInput.val() !== "";

      if (hasDate && hasTime) {
        $stepContainer.addClass("completed");
      }
    });
  }

  /**
   * Initialize photo upload functionality
   */
  function initPhotoUpload() {
    // Photo upload functionality
    $(".upload-photo-btn").on("click", function (e) {
      e.preventDefault();

      var button = $(this);
      var stepContainer = button.closest(".timeline-step");
      var photoPreview = button.siblings(".photo-preview");
      var hiddenInput = button.siblings('input[type="hidden"]');

      // WordPress media uploader
      var mediaUploader = wp.media({
        title: "Upload Photo Proof",
        button: {
          text: "Use this photo",
        },
        multiple: false,
        library: {
          type: "image",
        },
      });

      mediaUploader.on("select", function () {
        var attachment = mediaUploader
          .state()
          .get("selection")
          .first()
          .toJSON();

        // Update hidden input with attachment ID
        hiddenInput.val(attachment.id);

        // Show photo preview
        var imageHtml =
          '<img src="' +
          attachment.sizes.thumbnail.url +
          '" alt="Photo Proof" style="max-width: 150px; height: auto;">';
        imageHtml +=
          "<p><strong>File:</strong> " + attachment.filename + "</p>";
        imageHtml +=
          '<button type="button" class="button remove-photo-btn" style="margin-top: 5px;">Remove Photo</button>';

        photoPreview.html(imageHtml).show();
      });

      mediaUploader.open();
    });

    // Remove photo functionality
    $(document).on("click", ".remove-photo-btn", function (e) {
      e.preventDefault();

      var button = $(this);
      var photoPreview = button.closest(".photo-preview");
      var hiddenInput = photoPreview.siblings('input[type="hidden"]');

      hiddenInput.val("");
      photoPreview.html("").hide();
    });
  }

  /**
   * Auto-update order status based on timeline completion
   */
  function updateOrderStatus() {
    var completedSteps = $(".timeline-step.completed").length;
    var $statusSelect = $('select[name="order_status"]');

    if ($statusSelect.length && !$statusSelect.data("manual-override")) {
      if (completedSteps === 0) {
        $statusSelect.val("pending");
      } else if (completedSteps < 9) {
        $statusSelect.val("processing");
      } else if (completedSteps === 10) {
        $statusSelect.val("shipped");
      } else if (completedSteps === 11) {
        $statusSelect.val("delivered");
      }
      $statusSelect.trigger("change");
    }
  }

  /**
   * Initialize form validation
   */
  function initFormValidation() {
    // Price validation
    $("#custom_product_harga").on("blur", function () {
      var value = parseFloat($(this).val());
      var $errorMsg = $(this).siblings(".error-message");

      // Remove existing error
      $errorMsg.remove();
      $(this).removeClass("error");

      // Validate price
      if ($(this).val() !== "" && (isNaN(value) || value < 0)) {
        $(this).addClass("error");
        $(this).after(
          '<div class="error-message">Please enter a valid price (minimum 0)</div>'
        );
      }
    });

    // Form submission validation
    $("#post").on("submit", function (e) {
      var hasErrors = false;
      var errors = [];

      // Check price field
      var $priceInput = $("#custom_product_harga");
      if ($priceInput.length && $priceInput.val() !== "") {
        var price = parseFloat($priceInput.val());
        if (isNaN(price) || price < 0) {
          hasErrors = true;
          $priceInput.addClass("error");
          if (!$priceInput.siblings(".error-message").length) {
            $priceInput.after(
              '<div class="error-message">Please enter a valid price</div>'
            );
          }
          errors.push("Please enter a valid price");
        }
      }

      // Check if order has customer info when timeline steps are completed
      var hasCompletedSteps = $(".timeline-step.completed").length > 0;
      if (hasCompletedSteps) {
        if (!$('input[name="customer_name"]').val().trim()) {
          errors.push(
            "Customer Name is required when timeline steps are completed."
          );
          hasErrors = true;
        }
        if (!$('input[name="customer_phone"]').val().trim()) {
          errors.push(
            "Customer Phone is required when timeline steps are completed."
          );
          hasErrors = true;
        }
      }

      // Check delivery info for step 10
      var step10Completed = $('.timeline-step[data-step="10"]').hasClass(
        "completed"
      );
      if (step10Completed) {
        if (!$('input[name="delivery_driver_name"]').val().trim()) {
          errors.push(
            "Delivery Driver Name is required for completed delivery step."
          );
          hasErrors = true;
        }
        if (!$('input[name="delivery_driver_phone"]').val().trim()) {
          errors.push(
            "Delivery Driver Phone is required for completed delivery step."
          );
          hasErrors = true;
        }
      }

      if (hasErrors) {
        e.preventDefault();
        alert("Please fix the following errors:\n\n" + errors.join("\n"));
        return false;
      }
    });

    // Manual status override detection
    $('select[name="order_status"]').on("change", function () {
      $(this).data("manual-override", true);
    });
  }

  /**
   * Add success message after save
   */
  $(window).on("load", function () {
    // Check if post was just saved
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("message") === "1") {
      // Show success notification
      showNotification("Post saved successfully!", "success");
    }
  });

  /**
   * Show notification
   */
  function showNotification(message, type) {
    var $notification = $(
      '<div class="notice notice-' +
        type +
        ' is-dismissible"><p>' +
        message +
        "</p></div>"
    );
    $(".wrap h1").after($notification);

    // Auto hide after 3 seconds
    setTimeout(function () {
      $notification.fadeOut();
    }, 3000);
  }

  // Show/hide sections based on post type
  if ($("body").hasClass("post-type-custom_order")) {
    $(".order-meta-box").show();
  }

  if ($("body").hasClass("post-type-custom_product")) {
    $(".product-meta-box").show();
  }
});

// Add CSS for styling
jQuery(document).ready(function ($) {
  // Add inline styles for meta box elements
  var customCSS = `
        <style type="text/css">
        .custom-plugin-meta-box .error {
            border-color: #d32f2f !important;
            box-shadow: 0 0 0 1px #d32f2f !important;
        }
        .custom-plugin-meta-box .error-message {
            color: #d32f2f;
            font-size: 12px;
            margin-top: 5px;
            font-style: italic;
        }
        .custom-plugin-meta-box .price-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 2;
            pointer-events: none;
        }
        .custom-plugin-meta-box .price-input {
            position: relative;
        }
        .custom-plugin-meta-box .price-input input {
            padding-left: 35px !important;
        }
        #custom_order_status.status-pending, select[name="order_status"].status-pending { border-left: 4px solid #f57c00; }
        #custom_order_status.status-processing, select[name="order_status"].status-processing { border-left: 4px solid #1976d2; }
        #custom_order_status.status-shipped, select[name="order_status"].status-shipped { border-left: 4px solid #7b1fa2; }
        #custom_order_status.status-delivered, select[name="order_status"].status-delivered { border-left: 4px solid #388e3c; }
        #custom_order_status.status-cancelled, select[name="order_status"].status-cancelled { border-left: 4px solid #d32f2f; }
        </style>
    `;

  $("head").append(customCSS);
});
