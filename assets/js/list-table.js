/**
 * Custom Plugin List Table JavaScript
 */

jQuery(document).ready(function ($) {
  "use strict";

  // Initialize list table enhancements
  initListTableEnhancements();

  /**
   * Initialize list table enhancements
   */
  function initListTableEnhancements() {
    // Enhance image display
    enhanceImageDisplay();

    // Add quick edit functionality
    addQuickEditSupport();

    // Add bulk actions for status
    addBulkStatusActions();

    // Make table responsive
    makeTableResponsive();
  }

  /**
   * Enhance image display in list table
   */
  function enhanceImageDisplay() {
    $(".wp-list-table .column-featured_image img").each(function () {
      var $img = $(this);
      var $cell = $img.closest("td");

      // Add loading state
      $img.on("load", function () {
        $(this).fadeIn(200);
      });

      // Add click to view larger image
      $img.on("click", function (e) {
        e.preventDefault();
        var src = $(this).attr("src");
        var title = $(this).closest("tr").find(".row-title").text();

        // Create modal for larger image view
        showImageModal(src, title);
      });

      // Add hover effect
      $img.hover(
        function () {
          $(this).css("transform", "scale(1.1)");
          $(this).css("transition", "transform 0.2s ease");
        },
        function () {
          $(this).css("transform", "scale(1)");
        }
      );
    });
  }

  /**
   * Show image in modal
   */
  function showImageModal(src, title) {
    var modal = $('<div class="image-modal-overlay">');
    var modalContent = $('<div class="image-modal-content">');
    var image = $('<img src="' + src + '" alt="' + title + '">');
    var closeBtn = $('<span class="modal-close">&times;</span>');
    var titleEl = $("<h3>" + title + "</h3>");

    modalContent.append(closeBtn, titleEl, image);
    modal.append(modalContent);

    // Add to body
    $("body").append(modal);

    // Show modal
    modal.fadeIn(200);

    // Close on click outside or close button
    modal.on("click", function (e) {
      if (e.target === this || $(e.target).hasClass("modal-close")) {
        modal.fadeOut(200, function () {
          modal.remove();
        });
      }
    });

    // Close on ESC key
    $(document).on("keyup.modal", function (e) {
      if (e.keyCode === 27) {
        modal.fadeOut(200, function () {
          modal.remove();
        });
        $(document).off("keyup.modal");
      }
    });
  }

  /**
   * Add quick edit support for custom fields
   */
  function addQuickEditSupport() {
    // Store original quick edit function
    var $inlineEditPost = inlineEditPost;

    if (typeof $inlineEditPost !== "undefined") {
      inlineEditPost.edit = function (id) {
        // Call original function
        $inlineEditPost.edit.call(this, id);

        var postId = 0;
        if (typeof id === "object") {
          postId = parseInt(this.getId(id));
        }

        if (postId > 0) {
          // Get current values from the row
          var $row = $("#post-" + postId);
          var $editRow = $("#edit-" + postId);

          // Add custom fields to quick edit
          addCustomQuickEditFields($editRow, $row);
        }
      };
    }
  }

  /**
   * Add custom fields to quick edit form
   */
  function addCustomQuickEditFields($editRow, $row) {
    // For products - add price field
    if ($row.find(".column-product_price").length) {
      var currentPrice = $row
        .find(".column-product_price strong")
        .text()
        .replace(/[^0-9.,]/g, "");

      var priceField = $(
        '<label class="inline-edit-group">' +
          '<span class="title">Harga</span>' +
          '<input type="number" name="custom_product_harga" value="' +
          currentPrice +
          '" step="0.01" min="0">' +
          "</label>"
      );

      $editRow
        .find(".inline-edit-col-right .inline-edit-col")
        .append(priceField);
    }

    // For orders - add status field
    if ($row.find(".column-order_status").length) {
      var currentStatus = $row.find(".column-order_status span").attr("class");
      currentStatus = currentStatus
        ? currentStatus.replace("status-", "")
        : "pending";

      var statusOptions = [
        { value: "pending", label: "Pending" },
        { value: "processing", label: "Processing" },
        { value: "shipped", label: "Shipped" },
        { value: "delivered", label: "Delivered" },
        { value: "cancelled", label: "Cancelled" },
      ];

      var statusSelect = $('<select name="custom_order_status">');
      $.each(statusOptions, function (i, option) {
        var selected = option.value === currentStatus ? " selected" : "";
        statusSelect.append(
          '<option value="' +
            option.value +
            '"' +
            selected +
            ">" +
            option.label +
            "</option>"
        );
      });

      var statusField = $(
        '<label class="inline-edit-group">' +
          '<span class="title">Status</span>' +
          "</label>"
      ).append(statusSelect);

      $editRow
        .find(".inline-edit-col-right .inline-edit-col")
        .append(statusField);
    }
  }

  /**
   * Add bulk actions for status changes
   */
  function addBulkStatusActions() {
    if ($(".wp-list-table").data("post-type") === "custom_order") {
      var $bulkSelect = $('.bulkactions select[name="action"]');
      var $bulkSelect2 = $('.bulkactions select[name="action2"]');

      // Add status change options
      var statusOptions = [
        { value: "mark_pending", text: "Mark as Pending" },
        { value: "mark_processing", text: "Mark as Processing" },
        { value: "mark_shipped", text: "Mark as Shipped" },
        { value: "mark_delivered", text: "Mark as Delivered" },
        { value: "mark_cancelled", text: "Mark as Cancelled" },
      ];

      $.each(statusOptions, function (i, option) {
        $bulkSelect.append(
          '<option value="' + option.value + '">' + option.text + "</option>"
        );
        $bulkSelect2.append(
          '<option value="' + option.value + '">' + option.text + "</option>"
        );
      });
    }
  }

  /**
   * Make table responsive
   */
  function makeTableResponsive() {
    $(window)
      .on("resize", function () {
        var windowWidth = $(window).width();

        if (windowWidth < 1200) {
          $(
            ".wp-list-table .column-featured_image, .wp-list-table .column-product_price"
          ).hide();
        } else {
          $(
            ".wp-list-table .column-featured_image, .wp-list-table .column-product_price"
          ).show();
        }

        if (windowWidth < 768) {
          $(".wp-list-table .column-order_status").hide();
        } else {
          $(".wp-list-table .column-order_status").show();
        }
      })
      .trigger("resize");
  }

  // Add CSS for modal and enhancements
  addCustomCSS();

  /**
   * Add custom CSS for enhancements
   */
  function addCustomCSS() {
    var css = `
            <style type="text/css">
            .image-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                z-index: 100000;
                display: none;
            }
            
            .image-modal-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 90%;
                max-height: 90%;
                text-align: center;
            }
            
            .image-modal-content img {
                max-width: 100%;
                max-height: 70vh;
                object-fit: contain;
            }
            
            .modal-close {
                position: absolute;
                top: 10px;
                right: 15px;
                font-size: 24px;
                cursor: pointer;
                color: #999;
            }
            
            .modal-close:hover {
                color: #333;
            }
            
            .wp-list-table .column-featured_image img {
                cursor: pointer;
                transition: transform 0.2s ease;
            }
            
            .wp-list-table .column-product_price {
                font-family: 'Courier New', monospace;
            }
            
            .inline-edit-group input[type="number"] {
                width: 100px;
            }
            
            .inline-edit-group select {
                width: 120px;
            }
            </style>
        `;

    $("head").append(css);
  }
});
