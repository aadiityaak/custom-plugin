/**
 * Custom Plugin Frontend JavaScript
 */

(function ($) {
  "use strict";

  $(document).ready(function () {
    initCustomForm();
    initCustomMessages();
  });

  /**
   * Initialize custom form functionality
   */
  function initCustomForm() {
    $("#custom-plugin-form").on("submit", function (e) {
      e.preventDefault();

      var $form = $(this);
      var $button = $form.find('button[type="submit"]');
      var $response = $("#custom-plugin-response");

      // Validate form
      if (!validateForm($form)) {
        return false;
      }

      // Show loading state
      $button.prop("disabled", true).text("Submitting...");
      $response.hide();

      // Prepare form data
      var formData = {
        action: "custom_plugin_save_data",
        name: $form.find("#cp_name").val(),
        email: $form.find("#cp_email").val(),
        message: $form.find("#cp_message").val(),
        nonce: getCustomPluginNonce(),
      };

      // Submit via AJAX
      $.ajax({
        url: getAjaxUrl(),
        type: "POST",
        data: formData,
        timeout: 10000,
        success: function (response) {
          if (response.success) {
            showResponse(
              "Thank you! Your message has been submitted successfully.",
              "success"
            );
            $form[0].reset();
          } else {
            showResponse(
              response.data || "An error occurred. Please try again.",
              "error"
            );
          }
        },
        error: function (xhr, status, error) {
          var message =
            "Network error. Please check your connection and try again.";
          if (status === "timeout") {
            message = "Request timed out. Please try again.";
          }
          showResponse(message, "error");
        },
        complete: function () {
          $button.prop("disabled", false).text("Submit");
        },
      });
    });

    // Real-time form validation
    $("#custom-plugin-form input, #custom-plugin-form textarea").on(
      "blur",
      function () {
        validateField($(this));
      }
    );

    // Character counter for message field
    $("#cp_message").on("input", function () {
      var maxLength = 500;
      var currentLength = $(this).val().length;
      var remaining = maxLength - currentLength;

      var $counter = $(this).siblings(".char-counter");
      if ($counter.length === 0) {
        $counter = $('<div class="char-counter"></div>');
        $(this).after($counter);
      }

      $counter.text(remaining + " characters remaining");

      if (remaining < 0) {
        $counter.addClass("over-limit");
        $(this).addClass("error");
      } else {
        $counter.removeClass("over-limit");
        $(this).removeClass("error");
      }
    });
  }

  /**
   * Validate entire form
   */
  function validateForm($form) {
    var isValid = true;

    $form.find("input[required], textarea[required]").each(function () {
      if (!validateField($(this))) {
        isValid = false;
      }
    });

    return isValid;
  }

  /**
   * Validate individual field
   */
  function validateField($field) {
    var value = $field.val().trim();
    var fieldType = $field.attr("type");
    var isRequired = $field.attr("required");
    var isValid = true;

    // Remove existing error states
    $field.removeClass("error");
    $field.siblings(".field-error").remove();

    // Check if required field is empty
    if (isRequired && value === "") {
      showFieldError($field, "This field is required.");
      isValid = false;
    }

    // Validate email format
    if (fieldType === "email" && value !== "") {
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        showFieldError($field, "Please enter a valid email address.");
        isValid = false;
      }
    }

    // Validate name field (only letters and spaces)
    if ($field.attr("id") === "cp_name" && value !== "") {
      var nameRegex = /^[a-zA-Z\s]+$/;
      if (!nameRegex.test(value)) {
        showFieldError($field, "Name should contain only letters and spaces.");
        isValid = false;
      }
    }

    // Validate message length
    if ($field.attr("id") === "cp_message" && value.length > 500) {
      showFieldError(
        $field,
        "Message is too long. Maximum 500 characters allowed."
      );
      isValid = false;
    }

    return isValid;
  }

  /**
   * Show field error
   */
  function showFieldError($field, message) {
    $field.addClass("error");
    var $error = $('<div class="field-error">' + message + "</div>");
    $field.after($error);
  }

  /**
   * Show response message
   */
  function showResponse(message, type) {
    var $response = $("#custom-plugin-response");
    $response
      .removeClass("success error")
      .addClass(type)
      .text(message)
      .slideDown();

    // Auto-hide success messages
    if (type === "success") {
      setTimeout(function () {
        $response.slideUp();
      }, 5000);
    }
  }

  /**
   * Initialize custom message animations
   */
  function initCustomMessages() {
    $(".custom-plugin-message").each(function () {
      var $this = $(this);

      // Add fade-in animation
      $this.css("opacity", "0").animate({ opacity: 1 }, 800);

      // Add close button for dismissible messages
      if ($this.hasClass("dismissible")) {
        var $closeBtn = $(
          '<button class="message-close" aria-label="Close">&times;</button>'
        );
        $this.append($closeBtn);

        $closeBtn.on("click", function () {
          $this.fadeOut(300, function () {
            $(this).remove();
          });
        });
      }
    });
  }

  /**
   * Get AJAX URL
   */
  function getAjaxUrl() {
    // Try to get from localized script first
    if (typeof customPluginAjax !== "undefined" && customPluginAjax.ajax_url) {
      return customPluginAjax.ajax_url;
    }

    // Fallback to WordPress default
    if (typeof ajaxurl !== "undefined") {
      return ajaxurl;
    }

    // Last resort - construct manually
    return window.location.origin + "/wp-admin/admin-ajax.php";
  }

  /**
   * Get nonce for AJAX requests
   */
  function getCustomPluginNonce() {
    if (typeof customPluginAjax !== "undefined" && customPluginAjax.nonce) {
      return customPluginAjax.nonce;
    }

    // Try to find nonce in form
    var $nonce = $('input[name*="custom_plugin"]').first();
    if ($nonce.length) {
      return $nonce.val();
    }

    return "";
  }

  /**
   * Smooth scroll to response message
   */
  function scrollToResponse() {
    var $response = $("#custom-plugin-response");
    if ($response.length && $response.is(":visible")) {
      $("html, body").animate(
        {
          scrollTop: $response.offset().top - 50,
        },
        500
      );
    }
  }

  /**
   * Initialize accessibility features
   */
  function initAccessibility() {
    // Add ARIA labels for form validation
    $("#custom-plugin-form input, #custom-plugin-form textarea")
      .on("invalid", function () {
        $(this).attr("aria-invalid", "true");
      })
      .on("input", function () {
        if (this.validity.valid) {
          $(this).removeAttr("aria-invalid");
        }
      });

    // Keyboard navigation for custom elements
    $(".custom-plugin-message .message-close").on("keydown", function (e) {
      if (e.which === 13 || e.which === 32) {
        // Enter or Space
        e.preventDefault();
        $(this).click();
      }
    });
  }

  // Initialize accessibility features
  initAccessibility();
})(jQuery);
