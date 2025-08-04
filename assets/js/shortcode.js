jQuery(document).ready(function ($) {
  "use strict";

  // Order Form Functionality
  const orderForm = $("#custom-order-form");
  const productSelect = $("#order_product_id");
  const quantityInput = $("#order_quantity");
  const totalPriceText = $("#total_price_text");
  const totalPriceHidden = $("#order_total_price");
  const submitBtn = $(".submit-order-btn");
  const btnText = $(".btn-text");
  const btnLoading = $(".btn-loading");
  const successMessage = $(".success-message");
  const errorMessage = $(".error-message");

  // Calculate total price
  function calculateTotal() {
    let price = 0;

    // Get price from selected product
    if (productSelect.length) {
      const selectedOption = productSelect.find("option:selected");
      price = parseFloat(selectedOption.data("price")) || 0;
    } else {
      // Get price from hidden product (single product page)
      const productPrice = $(".product-price");
      if (productPrice.length) {
        price = parseFloat(productPrice.data("price")) || 0;
      }
    }

    const quantity = parseInt(quantityInput.val()) || 1;
    const total = price * quantity;

    // Update display
    totalPriceText.text("Rp " + formatNumber(total));
    totalPriceHidden.val(total);
  }

  // Format number with thousand separators
  function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  // Event listeners for price calculation
  productSelect.on("change", calculateTotal);
  quantityInput.on("input", calculateTotal);

  // Initialize calculation on page load
  calculateTotal();

  // Form submission handler
  orderForm.on("submit", function (e) {
    e.preventDefault();

    // Validate form
    if (!validateForm()) {
      return false;
    }

    // Show loading state
    submitBtn.prop("disabled", true);
    btnText.hide();
    btnLoading.show();

    // Hide previous messages
    successMessage.hide();
    errorMessage.hide();

    // Prepare form data
    const formData = new FormData(this);
    formData.append("action", "submit_custom_order");

    // Submit via AJAX
    $.ajax({
      url: customPluginAjax.ajaxurl,
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          // Show success message
          successMessage.html("<p>" + response.data.message + "</p>").show();

          // Reset form
          orderForm[0].reset();
          calculateTotal();

          // Scroll to success message
          $("html, body").animate(
            {
              scrollTop: successMessage.offset().top - 100,
            },
            500
          );

          // Optional: Redirect after delay
          if (response.data.redirect_url) {
            setTimeout(function () {
              window.location.href = response.data.redirect_url;
            }, 3000);
          }
        } else {
          // Show error message
          errorMessage
            .html(
              "<p>" +
                (response.data.message || customPluginAjax.messages.error) +
                "</p>"
            )
            .show();

          // Scroll to error message
          $("html, body").animate(
            {
              scrollTop: errorMessage.offset().top - 100,
            },
            500
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        errorMessage
          .html("<p>" + customPluginAjax.messages.error + "</p>")
          .show();

        // Scroll to error message
        $("html, body").animate(
          {
            scrollTop: errorMessage.offset().top - 100,
          },
          500
        );
      },
      complete: function () {
        // Reset button state
        submitBtn.prop("disabled", false);
        btnText.show();
        btnLoading.hide();
      },
    });
  });

  // Form validation
  function validateForm() {
    let isValid = true;
    const requiredFields = orderForm.find("[required]");

    // Remove previous error styling
    requiredFields.removeClass("error");

    requiredFields.each(function () {
      const field = $(this);
      const value = field.val().trim();

      if (!value) {
        field.addClass("error");
        isValid = false;
      }

      // Email validation
      if (field.attr("type") === "email" && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          field.addClass("error");
          isValid = false;
        }
      }

      // Phone validation (simple Indonesian format)
      if (field.attr("name") === "customer_phone" && value) {
        const phoneRegex = /^(\+62|62|0)[0-9]{9,13}$/;
        if (!phoneRegex.test(value.replace(/\s+/g, ""))) {
          field.addClass("error");
          isValid = false;
        }
      }
    });

    if (!isValid) {
      errorMessage
        .html("<p>Silakan lengkapi semua field yang wajib dengan benar.</p>")
        .show();

      // Scroll to first error field
      const firstError = orderForm.find(".error").first();
      if (firstError.length) {
        $("html, body").animate(
          {
            scrollTop: firstError.offset().top - 150,
          },
          500
        );
      }
    }

    return isValid;
  }

  // Phone number formatting
  $('input[name="customer_phone"]').on("input", function () {
    let value = $(this).val().replace(/\D/g, "");

    // Limit length
    if (value.length > 13) {
      value = value.substr(0, 13);
    }

    // Format display
    if (value.length > 0) {
      if (value.startsWith("62")) {
        value = "+" + value;
      } else if (value.startsWith("0")) {
        // Keep as is for local format
      } else {
        // Add 0 prefix if missing
        value = "0" + value;
      }
    }

    $(this).val(value);
  });

  // Input field enhancements
  orderForm.find("input, textarea, select").on("focus", function () {
    $(this).removeClass("error");
  });

  // Dismissible messages
  $(document).on("click", ".dismiss-btn", function () {
    $(this).closest(".custom-plugin-message").fadeOut();
  });

  // Auto-hide messages after delay
  function autoHideMessage(element, delay = 5000) {
    setTimeout(function () {
      element.fadeOut();
    }, delay);
  }

  // Smooth animations for form sections
  $(".form-section").each(function (index) {
    $(this).css({
      opacity: "0",
      transform: "translateY(20px)",
    });

    setTimeout(function () {
      $(".form-section").eq(index).css({
        transition: "all 0.6s ease",
        opacity: "1",
        transform: "translateY(0)",
      });
    }, index * 100);
  });

  // Product card hover effects
  $(".product-card").hover(
    function () {
      $(this).css({
        transform: "scale(1.02)",
        transition: "transform 0.3s ease",
      });
    },
    function () {
      $(this).css({
        transform: "scale(1)",
        transition: "transform 0.3s ease",
      });
    }
  );

  // Quantity input validation
  quantityInput.on("input", function () {
    let value = parseInt($(this).val());

    if (value < 1 || isNaN(value)) {
      $(this).val(1);
    }

    if (value > 9999) {
      $(this).val(9999);
    }
  });

  // Enhanced form field animations
  orderForm.find("input, textarea, select").each(function () {
    const field = $(this);
    const group = field.closest(".form-group");

    field.on("focus", function () {
      group.addClass("focused");
    });

    field.on("blur", function () {
      if (!field.val()) {
        group.removeClass("focused");
      }
    });

    // Check if field has value on load
    if (field.val()) {
      group.addClass("focused");
    }
  });

  // Scroll to top when form is submitted successfully
  function scrollToTop() {
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      800
    );
  }

  // Loading animation for submit button
  function animateButton(isLoading) {
    if (isLoading) {
      submitBtn.addClass("loading");
    } else {
      submitBtn.removeClass("loading");
    }
  }

  // Enhanced error styling
  const errorStyle = {
    "border-color": "#e53e3e",
    "box-shadow": "0 0 0 3px rgba(229, 62, 62, 0.1)",
  };

  const successStyle = {
    "border-color": "#38a169",
    "box-shadow": "0 0 0 3px rgba(56, 161, 105, 0.1)",
  };

  // Real-time validation feedback
  orderForm
    .find("input[required], textarea[required], select[required]")
    .on("blur", function () {
      const field = $(this);
      const value = field.val().trim();

      if (value) {
        field.css(successStyle);
        setTimeout(function () {
          field.css({
            "border-color": "#e2e8f0",
            "box-shadow": "none",
          });
        }, 2000);
      }
    });

  // Initialize tooltips if needed
  $("[data-tooltip]").each(function () {
    const tooltip = $('<div class="tooltip"></div>').text(
      $(this).data("tooltip")
    );
    $("body").append(tooltip);

    $(this).hover(
      function (e) {
        tooltip.css({
          display: "block",
          left: e.pageX + 10,
          top: e.pageY - 30,
        });
      },
      function () {
        tooltip.hide();
      }
    );
  });

  // Console log for debugging
  console.log("Custom Plugin Shortcode JavaScript loaded successfully");

  // Check if AJAX object is available
  if (typeof customPluginAjax === "undefined") {
    console.warn(
      "customPluginAjax object not found. AJAX functionality may not work."
    );
  }
});
