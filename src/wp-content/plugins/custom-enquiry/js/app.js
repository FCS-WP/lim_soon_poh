//Single Product Enquiry
jQuery(document).ready(function ($) {
  function trigger_update_enquiry_counter() {
    const enquiryCounter = $(".enquiry-cart-badge");
    if (enquiryCounter) {
      const count = parseInt(enquiryCounter.text());
      enquiryCounter.html(count + 1);
    }
  }

  var $enquiryButton = $(".enquiry-single-button");

  if ($enquiryButton.length) {
    $enquiryButton.on("click", function (e) {
      var productId = $(this).attr("product-id") ?? $(this).data("product-id");
      let successMessage = $(this).siblings(".success-message");
      console.log("successMessage", successMessage)
      successMessage = successMessage ? successMessage : $("#success-message");
      var quantity = 1;
      var productsToAdd = {
        enquiry: [
          {
            productId: productId,
            quantity: quantity,
          },
        ],
      };

      if (productsToAdd.enquiry.length > 0) {
        $.ajax({
          url: wc_add_to_cart_params.ajax_url,
          type: "POST",
          data: {
            action: "add_to_enquiry_cart",
            products: productsToAdd,
          },
          success: function (response) {
            successMessage.show();
            trigger_update_enquiry_counter();
          },
          error: function () {
            alert("Failed to add product to enquiry cart.");
          },
        });
      } else {
        alert("No product to add.");
      }
    });
  }
});

// Page Enquiry
jQuery(document).ready(function ($) {
  var $enquiryButton = $("#enquiry-button");

  if ($enquiryButton.length) {
    $enquiryButton.on("click", function () {
      var productsToAdd = { enquiry: [] };

      $(".quantity input.qty").each(function () {
        var quantity = parseInt($(this).val(), 10);
        var variationId = $(this).data("variation-id");

        if (quantity > 0) {
          productsToAdd.enquiry.push({
            variationId: variationId,
            quantity: quantity,
          });
        }
      });

      if (productsToAdd.enquiry.length > 0) {
        $.ajax({
          url: wc_add_to_cart_params.ajax_url,
          type: "POST",
          data: {
            action: "add_multiple_to_cart_or_enquiry",
            products: productsToAdd,
          },
          success: function (response) {
            $("#success-message").show();
            setTimeout(function () {
              location.reload();
            }, 1500);
          },
          error: function () {
            alert("Failed to process items.");
          },
        });
      } else {
        alert("No products to add.");
      }
    });
  }

  // Variable Product Enquiry
  var $variableButton = $("#enquiry-variable-button");

  if ($variableButton.length) {
    $variableButton.on("click", function () {
      var quantity = parseInt($(".quantity input.qty").val(), 10);
      var variationId = $('input[name="variation_id"]').val();
      var productsToAdd = { enquiry: [] };

      if (variationId && quantity > 0) {
        productsToAdd.enquiry.push({
          variationId: variationId,
          quantity: quantity,
        });
      }

      if (productsToAdd.enquiry.length > 0) {
        $.ajax({
          url: wc_add_to_cart_params.ajax_url,
          type: "POST",
          data: {
            action: "add_variation_to_enquiry",
            products: productsToAdd,
          },
          success: function (response) {
            console.log("response", response)
            if (response.success) {
              $("#success-message").show();
              trigger_update_enquiry_counter();
            } else {
              alert(response.data);
            }
          },
          error: function () {
            alert("Failed to process items.");
          },
        });
      } else {
        alert("No products to add.");
      }
    });
  }
  // Handle quantity plus/minus
  $(".quantity-button").on("click", function () {
    var itemKey = $(this).data("item-key");
    var $inputField = $('input[name="quantity[' + itemKey + ']"]');
    var currentValue = parseInt($inputField.val());

    if ($(this).hasClass("minus")) {
      if (currentValue > 1) {
        $inputField.val(currentValue - 1);
      }
    } else if ($(this).hasClass("plus")) {
      $inputField.val(currentValue + 1);
    }
  });

  // Handle remove enquiry item
  $(".remove-enquiry-item").on("click", function (e) {
    e.preventDefault();
    var itemKey = $(this).data("item-key");
    var $loadingIndicator = $("#loadingIndicator");

    $.ajax({
      url: wc_add_to_cart_params.ajax_url,
      type: "POST",
      data: {
        action: "remove_enquiry_item",
        item_key: itemKey,
      },
      beforeSend: function () {
        $loadingIndicator?.show();
      },
      success: function (response) {
        if (response.success) {
          $('tr[data-item-key="' + itemKey + '"]').remove();

          if ($(".enquiry-cart-table tbody tr").length === 0) {
            $(".enquiry-cart-table").html(
              '<tbody><tr><td colspan="3">Your enquiry cart is empty.</td></tr></tbody>'
            );
          }

          location.reload(); // Optional — remove if you want to avoid reload
        } else {
          console.error("Error removing item:", response.data.message);
        }
      },
      error: function (error) {
        console.error("Error removing item:", error);
      },
      complete: function () {
        $loadingIndicator?.hide();
      },
    });
  });
});
// Remove Enquiry
jQuery(document).ready(function ($) {
  $(".remove-enquiry-items").on("click", function () {
    var itemKey = $(this).data("item-key");

    $.ajax({
      url: wc_add_to_cart_params.ajax_url,
      type: "POST",
      data: {
        action: "remove_enquiry_item",
        item_key: itemKey,
      },
      success: function (response) {
        console.log(response);

        var $row = $('tr[data-item-key="' + itemKey + '"]');
        if ($row.length) {
          $row.remove();
          location.reload(); // Optional: remove if not needed
        }

        if ($(".enquiry-cart-table tbody tr").length === 0) {
          $(".enquiry-cart").html("Your enquiry cart is empty.");
        }
      },
      error: function (error) {
        console.error("AJAX error:", error);
      },
    });
  });
});

jQuery(document).ready(function ($) {
  var $formCart = $(".elementor-add-to-cart form.cart");
  var $customElement = $(".custom-element");

  if ($("body").hasClass("single-product")) {
    if ($formCart.length === 0) {
      $customElement.removeClass("d-none").addClass("d-block");
    }
  }
});

jQuery(document).ready(function ($) {
  $(document).on("click", ".move-to-enquiry-link", function (e) {
    e.preventDefault();

    $.ajax({
      url: moveToEnquiry.ajax_url,
      type: "POST",
      data: {
        action: "move_to_enquiry",
      },
      success: function (response) {
        if (response.success) {
          window.location.href = response.data.redirect_url;
        } else {
          alert("Failed to move products to enquiry. Please try again.");
        }
      },
    });
  });
});

jQuery(document).ready(function ($) {
  $(".qty_button").on("click", function () {
    var itemKey = $(this).data("item-key");
    var $inputField = $('input[name="quantity[' + itemKey + ']"]');
    var currentValue = parseInt($inputField.val());

    if ($(this).hasClass("minus")) {
      if (currentValue > 1) {
        $inputField.val(currentValue - 1);
      }
    } else if ($(this).hasClass("plus")) {
      $inputField.val(currentValue + 1);
    }
  });

  $(document).on("submit", "#enquiry_cart_form", (e) => {
    e.preventDefault();
    const formData = $(e.currentTarget).serialize();

    $.ajax({
      url: wc_add_to_cart_params.ajax_url,
      type: "POST",
      data: formData + "&action=update_enquiry_cart",
      success: (response) => {
        if (response.success) {
          location.reload();
        } else {
          alert("Failed to update the cart.");
        }
      },
      error: (xhr, status, error) => {
        console.error("An error occurred:", error);
      },
    });
  });
});
