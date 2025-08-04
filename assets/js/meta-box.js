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
  }

  /**
   * Initialize status handling
   */
  function initStatusHandling() {
    var $statusSelect = $("#custom_order_status");

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
        }
      }

      if (hasErrors) {
        e.preventDefault();
        alert("Please fix the errors before saving.");
        return false;
      }
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
        #custom_order_status.status-pending { border-left: 4px solid #f57c00; }
        #custom_order_status.status-processing { border-left: 4px solid #1976d2; }
        #custom_order_status.status-shipped { border-left: 4px solid #7b1fa2; }
        #custom_order_status.status-delivered { border-left: 4px solid #388e3c; }
        #custom_order_status.status-cancelled { border-left: 4px solid #d32f2f; }
        </style>
    `;

  $("head").append(customCSS);
});
