/**
 * Custom Plugin Admin JavaScript
 */

jQuery(document).ready(function ($) {
  "use strict";

  // Admin dashboard functionality
  initAdminDashboard();

  /**
   * Initialize admin dashboard
   */
  function initAdminDashboard() {
    // Add animation to stat boxes
    $(".stat-box").hover(
      function () {
        $(this).css("transform", "scale(1.05)");
        $(this).css("transition", "transform 0.2s ease");
      },
      function () {
        $(this).css("transform", "scale(1)");
      }
    );

    // Make tables sortable if available
    if ($.fn.DataTable) {
      $(".wp-list-table").DataTable({
        pageLength: 10,
        responsive: true,
        language: {
          search: "Search submissions:",
          lengthMenu: "Show _MENU_ submissions per page",
          info: "Showing _START_ to _END_ of _TOTAL_ submissions",
          infoEmpty: "No submissions available",
          infoFiltered: "(filtered from _MAX_ total submissions)",
          paginate: {
            first: "First",
            last: "Last",
            next: "Next",
            previous: "Previous",
          },
        },
      });
    }

    // Settings form validation
    $("#custom-plugin-settings-form").on("submit", function (e) {
      var customMessage = $("#custom_message").val().trim();

      if (customMessage === "") {
        alert("Please enter a custom message.");
        e.preventDefault();
        return false;
      }

      // Show loading state
      $(this)
        .find('input[type="submit"]')
        .prop("disabled", true)
        .val("Saving...");
    });

    // Add confirmation for destructive actions
    $(".delete-action").on("click", function (e) {
      if (
        !confirm(
          "Are you sure you want to delete this item? This action cannot be undone."
        )
      ) {
        e.preventDefault();
        return false;
      }
    });

    // Auto-save settings
    $(".auto-save").on("change", function () {
      var $this = $(this);
      var setting = $this.attr("name");
      var value = $this.is(":checkbox") ? $this.is(":checked") : $this.val();

      $.ajax({
        url: customPluginAjax.ajax_url,
        type: "POST",
        data: {
          action: "custom_plugin_auto_save",
          setting: setting,
          value: value,
          nonce: customPluginAjax.nonce,
        },
        success: function (response) {
          if (response.success) {
            showNotice("Setting saved automatically.", "success");
          }
        },
        error: function () {
          showNotice("Failed to save setting.", "error");
        },
      });
    });
  }

  /**
   * Show admin notice
   */
  function showNotice(message, type) {
    var noticeClass = type === "success" ? "notice-success" : "notice-error";
    var $notice = $(
      '<div class="notice ' +
        noticeClass +
        ' is-dismissible"><p>' +
        message +
        "</p></div>"
    );

    $(".wrap h1").after($notice);

    setTimeout(function () {
      $notice.fadeOut(function () {
        $(this).remove();
      });
    }, 3000);
  }

  /**
   * Export data functionality
   */
  $("#export-data").on("click", function (e) {
    e.preventDefault();

    $.ajax({
      url: customPluginAjax.ajax_url,
      type: "POST",
      data: {
        action: "custom_plugin_export_data",
        nonce: customPluginAjax.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Create download link
          var blob = new Blob([response.data], { type: "text/csv" });
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement("a");
          a.href = url;
          a.download = "custom-plugin-data.csv";
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);

          showNotice("Data exported successfully.", "success");
        } else {
          showNotice("Failed to export data.", "error");
        }
      },
      error: function () {
        showNotice("Export failed. Please try again.", "error");
      },
    });
  });

  /**
   * Real-time preview for custom message
   */
  $("#custom_message").on("input", function () {
    var message = $(this).val();
    var $preview = $("#message-preview");

    if ($preview.length === 0) {
      $preview = $(
        '<div id="message-preview" class="custom-plugin-message" style="margin-top: 10px;"></div>'
      );
      $(this).after($preview);
    }

    if (message.trim() === "") {
      $preview.hide();
    } else {
      $preview.text(message).show();
    }
  });

  /**
   * Bulk actions
   */
  $("#doaction, #doaction2").on("click", function (e) {
    var action = $(this).siblings("select").val();
    var checkedItems = $('input[name="bulk-delete[]"]:checked');

    if (action === "delete" && checkedItems.length > 0) {
      if (!confirm("Are you sure you want to delete the selected items?")) {
        e.preventDefault();
        return false;
      }
    }
  });

  /**
   * Copy shortcode to clipboard
   */
  $(".copy-shortcode").on("click", function (e) {
    e.preventDefault();

    var shortcode = $(this).data("shortcode");
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(shortcode).select();
    document.execCommand("copy");
    $temp.remove();

    showNotice("Shortcode copied to clipboard!", "success");
  });
});
