$(document).ready(function () {
  $('.only-number-allowed').on('keypress', function (event) {
    if (event.which < 48 || event.which > 57) {
      event.preventDefault();
    }
  });

  $('.only-alphabet-allowed').on('keypress', function (event) {
    if (
      (event.which < 65 || event.which > 90) && // Uppercase A-Z
      (event.which < 97 || event.which > 122)  // Lowercase a-z
    ) {
      event.preventDefault();
    }
  });

  $('.only-alphabet-number-allowed').on('keypress', function (event) {
    if (
      (event.which < 48 || event.which > 57) && // Numbers 0-9
      (event.which < 65 || event.which > 90) && // Uppercase A-Z
      (event.which < 97 || event.which > 122)   // Lowercase a-z
    ) {
      event.preventDefault();
    }
  });

  $('.alpha-first-cap-num-sp-chars').on('keypress', function (event) {
    // Get the current value of the input
    let inputValue = $(this).val();

    // Capitalize the first letter of each word and convert the rest to lowercase
    let capitalizedValue = inputValue.toLowerCase().split(' ').map(function (word) {
      return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');

    // Update the input value with the capitalized text
    $(this).val(capitalizedValue);

    // Restrict characters while the user types
    // Check if the last entered character is valid
    let lastChar = capitalizedValue.charAt(capitalizedValue.length - 1);

    if (
      !(
        (lastChar >= '0' && lastChar <= '9') ||  // Numbers 0-9
        (lastChar >= 'A' && lastChar <= 'Z') ||  // Uppercase A-Z
        (lastChar >= 'a' && lastChar <= 'z') ||  // Lowercase a-z
        lastChar === ' ' ||  // Space
        lastChar === '/' ||  // Slash ("/")
        lastChar === '-' ||  // Hyphen ("-")
        lastChar === '@' ||  // At symbol ("@")
        lastChar === ':'     // Colon (":")
      )
    ) {
      // If the last character is not valid, remove it
      $(this).val(capitalizedValue.slice(0, -1));
    }
  });

  $('.alpha-single-space').on('keypress', function (event) {
    // Allow letters (A-Z, a-z) and a single space (ASCII 32)
    if (
      (event.which < 65 || event.which > 90) && // Uppercase A-Z
      (event.which < 97 || event.which > 122) && // Lowercase a-z
      event.which !== 32 // Space (ASCII code 32)
    ) {
      event.preventDefault(); // Prevent invalid character
    }
  });

  $('.first-char-capitalize').on('input', function () {
    // Split the value into words, capitalize each word, and rejoin with spaces
    let words = $(this).val().toLowerCase().split(' ');
    for (let i = 0; i < words.length; i++) {
      if (words[i].length > 0) {
        words[i] = words[i][0].toUpperCase() + words[i].substring(1);
      }
    }
    $(this).val(words.join(' '));
  });

  $('.each-char-capitalize').on('input', function () {
    let inputValue = $(this).val().toUpperCase();
    $(this).val(inputValue);
  });
  $('.pancard_no').on('input', function (event) {
    this.value = this.value.toUpperCase();
  });
  $("#submit-btn").click(function () {
    $("#admin_profile").submit();
    $("#submit-btn").prop("disabled", true);
  });
  $("#serviceType1").click(function () {
    if ($("#serviceType1").is(":checked")) {
      $("#aepsAmountBlock").css("display", "none");
    }
  });
  $("#serviceType2").click(function () {
    if ($("#serviceType2").is(":checked")) {
      $("#aepsAmountBlock").css("display", "none");
    }
  });
  $("#serviceType3").click(function () {
    $("#aepsAmountBlock").css("display", "block");
  });
  $("#serviceType4").click(function () {
    $("#aepsAmountBlock").css("display", "block");
  });
  $("#rechargeComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    if (memberID != 0) {
      $.ajax({
        url: siteUrl + "retailer/master/getRechargeCommData/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".recharge-comm-loader").html("");
            $("#recharge-comm-block").html(data["str"]);
          } else {
            $(".recharge-comm-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    } else {
      $(".recharge-comm-loader").html(
        '<font color="red">Member Not Valid.</font>'
      );
    }
  });

  $("#upi-topup-btn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".upi_loader").show();
    var str = $("#upi_topup_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/wallet/upiRequestAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 0) {
          $(".upi_loader").hide();
          if (data["is_api_error"] == 0) {
            $("#amount_error").html(data["amount_error"]);
            $("#vpa_error").html(data["vpa_error"]);
            $("#description_error").html(data["description_error"]);
          } else {
            $("#apiErrorResponse").html(
              '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
              data["message"] +
              "</div>"
            );
          }
        } else {
          $(".upi_loader").hide();
          $(".upi_request_loader").show();
          setTimeout(getUpiCallback(data["txnid"]), 3000);
        }
      }
    });
  });

  function getUpiCallback(txnid = "") {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/wallet/getUpiCallbackResponse/" + txnid,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          window.location.href = siteUrl + "retailer/wallet/sendRequest";
        } else {
          setTimeout(getUpiCallback(txnid), 3000);
        }
      }
    });
  }

  $("#upi-cash-topup-btn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".upi_loader").show();
    var str = $("#upi_topup_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/wallet/upiCashRequestAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 0) {
          $(".upi_loader").hide();
          if (data["is_api_error"] == 0) {
            $("#amount_error").html(data["amount_error"]);
            $("#vpa_error").html(data["vpa_error"]);
            $("#description_error").html(data["description_error"]);
          } else {
            $("#apiErrorResponse").html(
              '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
              data["message"] +
              "</div>"
            );
          }
        } else {
          $(".upi_loader").hide();
          $(".upi_request_loader").show();
          setTimeout(getUpiCashCallback(data["txnid"]), 3000);
        }
      }
    });
  });

  function getUpiCashCallback(txnid = "") {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/wallet/getUpiCashCallbackResponse/" + txnid,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          window.location.href = siteUrl + "retailer/wallet/sendCashRequest";
        } else {
          setTimeout(getUpiCashCallback(txnid), 3000);
        }
      }
    });
  }

  $("#qr-topup-btn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".upi_loader").show();
    var str = $("#qr_topup_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/upi/qrGenerateAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 0) {
          $(".upi_loader").hide();
          if (data["is_api_error"] == 0) {
            $("#amount_error").html(data["amount_error"]);
          } else {
            $("#apiErrorResponse").html(
              '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
              data["message"] +
              "</div>"
            );
          }
        } else {
          $(".upi_loader").hide();
          $("#qrModal").modal("show");
          $("#qr_div").html(data["qr_code"]);
        }
      }
    });
  });

  $("#bbpsComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    if (memberID != 0) {
      $.ajax({
        url: siteUrl + "retailer/master/getBBPSCommData/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".recharge-comm-loader").html("");
            $("#recharge-comm-block").html(data["str"]);
          } else {
            $(".recharge-comm-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    } else {
      $(".recharge-comm-loader").html(
        '<font color="red">Member Not Valid.</font>'
      );
    }
  });

  $("#bbpsLiveComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    if (memberID != 0) {
      $.ajax({
        url: siteUrl + "retailer/master/getBBPSCommData/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".recharge-comm-loader").html("");
            $("#recharge-comm-block").html(data["str"]);
          } else {
            $(".recharge-comm-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    } else {
      $(".recharge-comm-loader").html(
        '<font color="red">Member Not Valid.</font>'
      );
    }
  });

  $("#dmrComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    if (memberID != 0) {
      $.ajax({
        url: siteUrl + "retailer/master/getMemberDMRCommData/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".recharge-comm-loader").html("");
            $("#dmr-comm-block").html(data["str"]);
          } else {
            $(".recharge-comm-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    } else {
      $(".recharge-comm-loader").html(
        '<font color="red">Member Not Valid.</font>'
      );
    }
  });

  $("#aepsComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/master/getMemberAEPSCommData/" + memberID,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#dmr-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  });

  $("#serviceSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/master/getServiceData/" + memberID,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  });

  $("#selState").change(function () {
    var siteUrl = $("#siteUrl").val();
    var stateID = $(this).val();
    if (stateID) {
      $.ajax({
        url: siteUrl + "retailer/aeps/getCityList/" + stateID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#selCity").html(data["str"]);
          }
        }
      });
    }
  });

  $("#nsdlStateId").change(function () {
    var siteUrl = $("#siteUrl").val();
    var stateID = $(this).val();
    if (stateID) {
      $.ajax({
        url: siteUrl + "retailer/pancard/getNsdlDistrictList/" + stateID,
        success: function (r) {
          $("#nsdlDistrictId").html(r);
        }
      });
    }
  });

  $("#electricityOperator").change(function () {
    var operator_id = $(this).val();
    $("#field-block").css("display", "none");
    $("#name-field-block").css("display", "none");
    $("#amount-field-block").css("display", "none");
    if (operator_id == "") {
      $(".ajax-loader").html('<font color="red">Select a operator.</font>');
      $("#fetch_status").val(0);
      $("#fieldName").val("");
      $("#field-block").css("display", "none");
      $("#name-field-block").css("display", "none");
      $("#amount-field-block").css("display", "none");
    } else {
      var siteUrl = $("#siteUrl").val();
      $(".ajax-loader").html(
        "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
      );
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/recharge/fetchBiller/" + operator_id,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".ajax-loader").html("");
            $("#fetch_status").val(1);
            $("#fieldName").val(data["fieldName"]);
            $("#account_number").attr(
              "placeholder",
              "Enter " + data["fieldName"]
            );
            $("#customer_name").attr(
              "placeholder",
              "Enter " + data["fieldOther"]
            );
            $("#field-block").css("display", "block");
            $("#name-field-block").css("display", "block");
            $("#amount-field-block").css("display", "block");
          } else {
            $(".ajax-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
            $("#fetch_status").val(0);
            $("#fieldName").val("");
            $("#field-block").css("display", "none");
            $("#name-field-block").css("display", "none");
            $("#amount-field-block").css("display", "none");
          }
        }
      });
    }
  });

  $("#account_number").blur(function () {
    var operator_id = $("#electricityOperator").val();
    if (operator_id == "") {
      $(".ajax-loader").html('<font color="red">Select a operator.</font>');
      $("#fetch_status").val(0);
      $("#fieldName").val("");
      $("#field-block").css("display", "none");
      $("#name-field-block").css("display", "none");
      $("#amount-field-block").css("display", "none");
    } else {
      var siteUrl = $("#siteUrl").val();
      $(".ajax-loader").html(
        "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
      );
      var str = $("#electricity-form").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/recharge/fetchBillerDetail/" + operator_id,
        data: str,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".ajax-loader").html("");
            $(".electricity-biller-name").html(data["customername"]);
            $("#amount").val(data["amount"]);
            $("#reference_id").val(data["reference_id"]);
          } else {
            $(".ajax-loader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    }
  });

  $("#payolmobile").blur(function () {
    var mobile = $(this).val();
    if (mobile != "") {
      var siteUrl = $("#siteUrl").val();
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/wallet/getMemberName/" + mobile,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          $("#memberBlock").html(data["msg"]);
        }
      });
    }
  });

  $("#transfer_amount").keyup(function () {
    var transfer_amount = $(this).val();
    if ($.isNumeric(transfer_amount)) {
      var service_tax_percentage = parseFloat(
        $("#service_tax_percentage").val()
      );
      var service_amount = (
        (service_tax_percentage / 100) *
        parseInt(transfer_amount)
      ).toFixed(2);
      $("#service_tax").val(service_amount);
      $("#wallet_transfer_amount").val(
        (transfer_amount - service_amount).toFixed(2)
      );
    } else {
      $("#service_tax").val(0);
      $("#wallet_transfer_amount").val(0);
    }
  });

  $("#fund-transfer-amount").keyup(function () {
    var amount = parseFloat($(this).val());
    var balance = parseFloat($("#user-wallet-balance").val());
    var from_1 = parseFloat($("#fund-charge-from-1").val());
    var to_1 = parseFloat($("#fund-charge-to-1").val());
    var flat_1 = parseFloat($("#fund-charge-flat-1").val());

    var from_2 = parseFloat($("#fund-charge-from-2").val());
    var to_2 = parseFloat($("#fund-charge-to-2").val());
    var flat_2 = parseFloat($("#fund-charge-flat-2").val());

    var from_3 = parseFloat($("#fund-charge-from-3").val());
    var to_3 = parseFloat($("#fund-charge-to-3").val());
    var flat_3 = parseFloat($("#fund-charge-flat-3").val());

    if ($.isNumeric(amount)) {
      if (amount >= from_1 && amount <= to_1) {
        var charge_amount = flat_1;
        var total_amount = parseFloat(charge_amount) + parseFloat(amount);
        $("#total_wallet_deducation").val(total_amount);
      } else if (amount >= from_2 && amount <= to_2) {
        var charge_amount = flat_2;
        var total_amount = parseFloat(charge_amount) + parseFloat(amount);
        $("#total_wallet_deducation").val(total_amount);
      } else if (amount >= from_3 && amount <= to_3) {
        var charge_amount = flat_3;
        var total_amount = parseFloat(charge_amount) + parseFloat(amount);
        $("#total_wallet_deducation").val(total_amount);
      } else {
        $("#total_wallet_deducation").val(amount);
      }
    } else {
      $("#total_wallet_deducation").val(0);
    }
  });

  $("#selMember").change(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $(this).val();
    if (memberID) {
      $.ajax({
        url: siteUrl + "retailer/wallet/getMemberWalletBalance/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#balance").val(data["balance"]);
          }
        }
      });
    }
  });

  $("#selEwalletMember").change(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $(this).val();
    if (memberID) {
      $.ajax({
        url: siteUrl + "retailer/ewallet/getMemberWalletBalance/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#balance").val(data["balance"]);
          }
        }
      });
    }
  });

  $("#operator").change(function () {
    var op = $(this).val();
    $('#offerOperator option[value="' + op + '"]').prop("selected", true);
    $('#rofferOperator option[value="' + op + '"]').prop("selected", true);
  });

  $("#viewPlanSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $("#offerModal").modal("show");
    $("#offerLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#offerFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getOperatorPlanList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#offerLoader").html(data["str"]);
        } else {
          $("#offerLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  });

  $("#dthViewPlanSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $("#offerModal").modal("show");
    $("#offerLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#offerFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getDTHOperatorPlanList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#offerLoader").html(data["str"]);
        } else {
          $("#offerLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  });

  $("#offermobile").blur(function () {
    $("#roffermobile").val($(this).val());

    var siteUrl = $("#siteUrl").val();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getOperatorType/" + $(this).val(),
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $('#operator option[value="' + data["operator_id"] + '"]').prop(
            "selected",
            true
          );
        }
      }
    });
  });

  $("#rofferSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $("#rofferModal").modal("show");
    $("#rofferLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#rofferFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getRofferList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#rofferLoader").html(data["str"]);
        } else {
          $("#rofferLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  });

  $("#dthRofferSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $("#rofferModal").modal("show");
    $("#rofferLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#rofferFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getDTHRofferList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#rofferLoader").html(data["str"]);
        } else {
          $("#rofferLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  });

  $("#cardNumber").blur(function () {
    var siteUrl = $("#siteUrl").val();
    var cardNumber = $("#cardNumber").val();
    var operator = $("#operator").val();
    if (cardNumber != "" && operator != "") {
      $("#customerInfoLoader").html(
        "<center><img src='" +
        siteUrl +
        "skin/admin/images/large-loading.gif' width='100' /></center>"
      );
      var str = $("#admin_profile").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/recharge/getDTHCustomerInfo",
        data: str,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#customerInfoLoader").html("");
            $("#customerName").val(data["customerName"]);
            $("#amount").val(data["monthlyRechargeAmount"]);
            $("#balanceInfo").html(
              "Available Balance - &#8377; " + data["balance"]
            );
          } else {
            $("#customerInfoLoader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    }
  });

  $("#bbps-mobile-prepaid-btn").click(function () {
    $(this).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#mobile-prepaid-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-mobile-prepaid-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/mobilePrepaidAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#bbps-mobile-prepaid-btn").prop("disabled", false);
          $("#mobile-prepaid-loader").html(data["msg"]);
          document.getElementById("bbps-mobile-prepaid-form").reset();
        } else {
          $("#bbps-mobile-prepaid-btn").prop("disabled", false);
          $("#mobile-prepaid-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#mobilePostpaidOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        3,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#mobilepostpaid-form-block").html(data["str"]);
        if (data["is_fetch"] == 1) {
          $("#mobile-postpaid-fetch-block").css("display", "block");
        } else {
          $("#mobile-postpaid-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbps-mobile-postpaid-btn").click(function () {
    $(this).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#mobile-postpaid-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-mobile-postpaid-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/mobilePostpaidAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#bbps-mobile-postpaid-btn").prop("disabled", false);
          $("#mobile-postpaid-loader").html(data["msg"]);
          document.getElementById("bbps-mobile-postpaid-form").reset();
        } else {
          $("#bbps-mobile-postpaid-btn").prop("disabled", false);
          $("#mobile-postpaid-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#bbpsElectricityOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#electricity-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        4,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#electricity-loader").html("");
        $("#electricity-form-block").html(data["str"]);
        $("#electricity-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#electricity-fetch-block").css("display", "block");
        } else {
          $("#electricity-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbps-electricity-btn").click(function () {
    $(this).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#electricity-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-electricity-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/electricityAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#bbps-electricity-btn").prop("disabled", false);
          $("#electricity-loader").html(data["msg"]);
          document.getElementById("bbps-electricity-form").reset();
        } else {
          $("#bbps-electricity-btn").prop("disabled", false);
          $("#electricity-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#bbpsDTHOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#dth-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        1,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#dth-loader").html("");
        $("#dth-form-block").html(data["str"]);
        $("#dth-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#dth-fetch-block").css("display", "block");
        } else {
          $("#dth-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbps-dth-btn").click(function () {
    $(this).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#dth-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-dth-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/dthAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#bbps-dth-btn").prop("disabled", false);
          $("#dth-loader").html(data["msg"]);
          document.getElementById("bbps-dth-form").reset();
        } else {
          $("#bbps-dth-btn").prop("disabled", false);
          $("#dth-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#bbpsBroadbandPostpaidOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#boradband-postpaid-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        8,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#boradband-postpaid-loader").html("");
        $("#boradband-postpaid-form-block").html(data["str"]);
        $("#boradband-postpaid-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#boradband-postpaid-fetch-block").css("display", "block");
        } else {
          $("#boradband-postpaid-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsLandlinePostpaidOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#landline-postpaid-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        2,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#landline-postpaid-loader").html("");
        $("#landline-postpaid-form-block").html(data["str"]);
        $("#landline-postpaid-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#landline-postpaid-fetch-block").css("display", "block");
        } else {
          $("#landline-postpaid-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsWaterOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#water-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        7,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#water-loader").html("");
        $("#water-form-block").html(data["str"]);
        $("#water-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#water-fetch-block").css("display", "block");
        } else {
          $("#water-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsGasOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#gas-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        6,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#gas-loader").html("");
        $("#gas-form-block").html(data["str"]);
        $("#gas-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#gas-fetch-block").css("display", "block");
        } else {
          $("#gas-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsLPGGasOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#lpg-gas-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        11,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#lpg-gas-loader").html("");
        $("#lpg-gas-form-block").html(data["str"]);
        $("#lpg-gas-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#lpg-gas-fetch-block").css("display", "block");
        } else {
          $("#lpg-gas-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsLoanOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#loan-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        17,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#loan-loader").html("");
        $("#loan-form-block").html(data["str"]);
        $("#loan-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#loan-fetch-block").css("display", "block");
        } else {
          $("#loan-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsInsuranceOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#insurance-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        5,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#insurance-loader").html("");
        $("#insurance-form-block").html(data["str"]);
        $("#insurance-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#insurance-fetch-block").css("display", "block");
        } else {
          $("#insurance-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsEmiPaymentOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#emi-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        10,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#emi-loader").html("");
        $("#emi-form-block").html(data["str"]);
        $("#emi-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#emi-fetch-block").css("display", "block");
        } else {
          $("#emi-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsFastagOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#fastag-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        12,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#fastag-loader").html("");
        $("#fastag-form-block").html(data["str"]);
        $("#fastag-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#fastag-fetch-block").css("display", "block");
        } else {
          $("#fastag-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsCableOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#cable-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        9,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#cable-loader").html("");
        $("#cable-form-block").html(data["str"]);
        $("#cable-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#cable-fetch-block").css("display", "block");
        } else {
          $("#cable-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsHousingSocietyOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#housing-society-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        17,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#housing-society-loader").html("");
        $("#housing-society-form-block").html(data["str"]);
        $("#housing-society-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#housing-society-fetch-block").css("display", "block");
        } else {
          $("#housing-society-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsMunicipalTaxesOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#municipal-taxes-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        18,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#municipal-taxes-loader").html("");
        $("#municipal-taxes-form-block").html(data["str"]);
        $("#municipal-taxes-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#municipal-taxes-fetch-block").css("display", "block");
        } else {
          $("#municipal-taxes-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsMunicipalServicesOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#municipal-services-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        13,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#municipal-services-loader").html("");
        $("#municipal-services-form-block").html(data["str"]);
        $("#municipal-services-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#municipal-services-fetch-block").css("display", "block");
        } else {
          $("#municipal-services-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsSubscriptionOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#subscription-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        20,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#subscription-loader").html("");
        $("#subscription-form-block").html(data["str"]);
        $("#subscription-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#subscription-fetch-block").css("display", "block");
        } else {
          $("#subscription-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsHospitalOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#hospital-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        19,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#hospital-loader").html("");
        $("#hospital-form-block").html(data["str"]);
        $("#hospital-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#hospital-fetch-block").css("display", "block");
        } else {
          $("#hospital-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsCreditCardOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#credit-card-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        22,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#credit-card-loader").html("");
        $("#credit-card-form-block").html(data["str"]);
        $("#credit-card-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#credit-card-fetch-block").css("display", "block");
        } else {
          $("#credit-card-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsEntertainmentOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#entertainment-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        9,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#entertainment-loader").html("");
        $("#entertainment-form-block").html(data["str"]);
        $("#entertainment-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#entertainment-fetch-block").css("display", "block");
        } else {
          $("#entertainment-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsTravelOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#travel-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        21,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#travel-loader").html("");
        $("#travel-form-block").html(data["str"]);
        $("#travel-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#travel-fetch-block").css("display", "block");
        } else {
          $("#travel-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#bbpsClubOperator").change(function () {
    var siteUrl = $("#siteUrl").val();
    $("#club-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url:
        siteUrl +
        "retailer/bbps/checkOperatorFetchOption/" +
        billerID +
        "/" +
        24,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#club-loader").html("");
        $("#club-form-block").html(data["str"]);
        $("#club-submit-btn").css("display", "block");
        if (data["is_fetch"] == 1) {
          $("#club-fetch-block").css("display", "block");
        } else {
          $("#club-fetch-block").css("display", "none");
        }
      }
    });
  });

  $("#selDmtBenId").change(function () {
    var siteUrl = $("#siteUrl").val();
    var stateID = $(this).val();
    if (stateID) {
      $.ajax({
        url: siteUrl + "retailer/dmt/getBenData/" + stateID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#ben_account_no").val(data["account_no"]);
            $("#ben_ifsc").val(data["ifsc"]);
          } else {
            $("#ben_account_no").val("");
            $("#ben_ifsc").val("");
          }
        }
      });
    }
  });


  function fetchMobilePostpaidBill() {
    var mobile = $("#mobile-postpaid-number").val();
    if (mobile == "") {
      $("#mobile-postpaid-number").focus();
      return false;
    } else {
      var siteUrl = $("#siteUrl").val();
      $("#mobile-postpaid-loader").html(
        "<center><img src='" +
        siteUrl +
        "skin/admin/images/large-loading.gif' width='100' /></center>"
      );
      var str = $("#bbps-mobile-postpaid-form").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/bbps/fetchMobilePostpaidBill",
        data: str,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#mobile-postpaid-amount").val(data["amount"]);
            $("#mobile-postpaid-loader").html("");
          } else {
            $("#mobile-postpaid-amount").val(data["amount"]);
            $("#mobile-postpaid-loader").html("");
          }
        }
      });
    }
  }

  function fetchElectricityBill() {
    var siteUrl = $("#siteUrl").val();
    $("#electricity-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-electricity-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/fetchElectricityBill",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#electricity-amount").val(data["amount"]);
          $("#electricity-loader").html("");
          if (data["accountHolderName"] != "") {
            $("#electricity-account-holder-name").html(
              "<b>Account Holder Name - " + data["accountHolderName"] + "</b>"
            );
          } else {
            $("#electricity-account-holder-name").html("");
          }
        } else {
          $("#electricity-amount").val(data["amount"]);
          $("#electricity-loader").html("");
          $("#electricity-account-holder-name").html("");
        }
      }
    });
  }

  function fetchDTHBill() {
    var siteUrl = $("#siteUrl").val();
    $("#dth-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#bbps-dth-form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/fetchDTHBill",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#dth-amount").val(data["amount"]);
          $("#dth-loader").html("");
          if (data["accountHolderName"] != "") {
            $("#dth-account-holder-name").html(
              "<b>Account Holder Name - " + data["accountHolderName"] + "</b>"
            );
          } else {
            $("#dth-account-holder-name").html("");
          }
        } else {
          $("#dth-amount").val(data["amount"]);
          $("#dth-loader").html("");
          $("#dth-account-holder-name").html("");
        }
      }
    });
  }

  function fetchMasterBill(service_id) {
    var loaderID = "";
    var formID = "";
    var amountID = "";
    var accountHolderName = "";
    if (service_id == 19) {
      var loaderID = "boradband-postpaid-loader";
      var formID = "bbps-boradband-postpaid-form";
      var amountID = "boradband-postpaid-amount";
      var accountHolderName = "boradband-postpaid-account-holder-name";
    } else if (service_id == 2) {
      var loaderID = "landline-postpaid-loader";
      var formID = "bbps-landline-postpaid-form";
      var amountID = "landline-postpaid-amount";
      var accountHolderName = "landline-postpaid-account-holder-name";
    } else if (service_id == 7) {
      var loaderID = "water-loader";
      var formID = "bbps-water-form";
      var amountID = "water-amount";
      var accountHolderName = "water-account-holder-name";
    } else if (service_id == 10) {
      var loaderID = "emi-loader";
      var formID = "bbps-emi-payment-form";
      var amountID = "emi-amount";
      var accountHolderName = "emi-account-holder-name";
    } else if (service_id == 6) {
      var loaderID = "gas-loader";
      var formID = "bbps-gas-form";
      var amountID = "gas-amount";
      var accountHolderName = "gas-account-holder-name";
    } else if (service_id == 11) {
      var loaderID = "lpg-gas-loader";
      var formID = "bbps-lpg-gas-form";
      var amountID = "lpg-gas-amount";
      var accountHolderName = "lpg-gas-account-holder-name";
    } else if (service_id == 17) {
      var loaderID = "loan-loader";
      var formID = "bbps-loan-form";
      var amountID = "loan-amount";
      var accountHolderName = "loan-account-holder-name";
    } else if (service_id == 5) {
      var loaderID = "insurance-loader";
      var formID = "bbps-insurance-form";
      var amountID = "insurance-amount";
      var accountHolderName = "insurance-account-holder-name";
    } else if (service_id == 12) {
      var loaderID = "fastag-loader";
      var formID = "bbps-fastag-form";
      var amountID = "fastag-amount";
      var accountHolderName = "fastag-account-holder-name";
    } else if (service_id == 9) {
      var loaderID = "cable-loader";
      var formID = "bbps-cable-form";
      var amountID = "cable-amount";
      var accountHolderName = "cable-account-holder-name";
    }
    // else if(service_id == 17)
    // {
    // 	var loaderID = 'housing-society-loader';
    // 	var formID = 'bbps-housing-society-form';
    // 	var amountID = 'housing-society-amount';
    // 	var accountHolderName = 'housing-society-account-holder-name';
    // }
    else if (service_id == 18) {
      var loaderID = "municipal-taxes-loader";
      var formID = "bbps-municipal-taxes-form";
      var amountID = "municipal-taxes-amount";
      var accountHolderName = "municipal-taxes-account-holder-name";
    } else if (service_id == 13) {
      var loaderID = "municipal-services-loader";
      var formID = "bbps-municipal-services-form";
      var amountID = "municipal-services-amount";
      var accountHolderName = "municipal-services-account-holder-name";
    } else if (service_id == 22) {
      var loaderID = "obi-credit-card-loader";
      var formID = "bbps-credit-card-mobi-form";
      var amountID = "cc_amount";
      var accountHolderName = "credit-card-account-holder-name";
    }
    // else if(service_id == 19)
    // {
    // 	var loaderID = 'hospital-loader';
    // 	var formID = 'bbps-hospital-form';
    // 	var amountID = 'hospital-amount';
    // 	var accountHolderName = 'hospital-account-holder-name';
    // }
    // else if(service_id == 22)
    // {
    // 	var loaderID = 'credit-card-loader';
    // 	var formID = 'bbps-credit-card-form';
    // 	var amountID = 'credit-card-amount';
    // 	var accountHolderName = 'credit-card-account-holder-name';
    // }
    // else if(service_id == 9)
    // {
    // 	var loaderID = 'entertainment-loader';
    // 	var formID = 'bbps-entertainment-form';
    // 	var amountID = 'entertainment-amount';
    // 	var accountHolderName = 'entertainment-account-holder-name';
    // }
    // else if(service_id == 21)
    // {
    // 	var loaderID = 'travel-loader';
    // 	var formID = 'bbps-travel-form';
    // 	var amountID = 'travel-amount';
    // 	var accountHolderName = 'travel-account-holder-name';
    // }
    // else if(service_id == 24)
    // {
    // 	var loaderID = 'club-loader';
    // 	var formID = 'bbps-club-form';
    // 	var amountID = 'club-amount';
    // 	var accountHolderName = 'club-account-holder-name';
    // }
    var siteUrl = $("#siteUrl").val();
    $("#" + loaderID).html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#" + formID).serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/fetchMasterBill/" + service_id,
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#" + amountID).val(data["amount"]);
          $("#" + loaderID).html("");
          if (data["accountHolderName"] != "") {
            $("#" + accountHolderName).html(
              "<b>Account Holder Name - " + data["accountHolderName"] + "</b>"
            );
          } else {
            $("#" + accountHolderName).html("");
          }
        } else {
          $("#" + amountID).val(data["amount"]);
          $("#" + loaderID).html("");
          $("#" + accountHolderName).html("");
        }
      }
    });
  }

  function payMasterBill(service_id) {
    var btnID = "";
    var loaderID = "";
    var formID = "";
    if (service_id == 19) {
      var loaderID = "boradband-postpaid-loader";
      var formID = "bbps-boradband-postpaid-form";
      var btnID = "bbps-boradband-postpaid-btn";
    } else if (service_id == 2) {
      var loaderID = "landline-postpaid-loader";
      var formID = "bbps-landline-postpaid-form";
      var btnID = "bbps-landline-postpaid-btn";
    } else if (service_id == 7) {
      var loaderID = "water-loader";
      var formID = "bbps-water-form";
      var btnID = "bbps-water-btn";
    } else if (service_id == 10) {
      var loaderID = "emi-loader";
      var formID = "bbps-emi-payment-form";
      var btnID = "emi-payment-btn";
    } else if (service_id == 6) {
      var loaderID = "gas-loader";
      var formID = "bbps-gas-form";
      var btnID = "bbps-gas-btn";
    } else if (service_id == 11) {
      var loaderID = "lpg-gas-loader";
      var formID = "bbps-lpg-gas-form";
      var btnID = "bbps-lpg-gas-btn";
    } else if (service_id == 17) {
      var loaderID = "loan-loader";
      var formID = "bbps-loan-form";
      var btnID = "bbps-loan-btn";
    } else if (service_id == 5) {
      var loaderID = "insurance-loader";
      var formID = "bbps-insurance-form";
      var btnID = "bbps-insurance-btn";
    } else if (service_id == 12) {
      var loaderID = "fastag-loader";
      var formID = "bbps-fastag-form";
      var btnID = "bbps-fastag-btn";
    } else if (service_id == 9) {
      var loaderID = "cable-loader";
      var formID = "bbps-cable-form";
      var btnID = "bbps-cable-btn";
    }
    // else if(service_id == 17)
    // {
    // 	var loaderID = 'housing-society-loader';
    // 	var formID = 'bbps-housing-society-form';
    // 	var btnID = 'bbps-housing-society-btn';
    // }
    else if (service_id == 18) {
      var loaderID = "municipal-taxes-loader";
      var formID = "bbps-municipal-taxes-form";
      var btnID = "bbps-municipal-taxes-btn";
    } else if (service_id == 13) {
      var loaderID = "municipal-services-loader";
      var formID = "bbps-municipal-services-form";
      var btnID = "bbps-municipal-services-btn";
    }
    // else if(service_id == 20)
    // {
    // 	var loaderID = 'subscription-loader';
    // 	var formID = 'bbps-subscription-form';
    // 	var btnID = 'bbps-subscription-btn';
    // }
    // else if(service_id == 19)
    // {
    // 	var loaderID = 'hospital-loader';
    // 	var formID = 'bbps-hospital-form';
    // 	var btnID = 'bbps-hospital-btn';
    // }
    // else if(service_id == 22)
    // {
    // 	var loaderID = 'credit-card-loader';
    // 	var formID = 'bbps-credit-card-form';
    // 	var btnID = 'bbps-credit-card-btn';
    // }
    // else if(service_id == 9)
    // {
    // 	var loaderID = 'entertainment-loader';
    // 	var formID = 'bbps-entertainment-form';
    // 	var btnID = 'bbps-entertainment-btn';
    // }
    // else if(service_id == 21)
    // {
    // 	var loaderID = 'travel-loader';
    // 	var formID = 'bbps-travel-form';
    // 	var btnID = 'bbps-travel-btn';
    // }
    // else if(service_id == 24)
    // {
    // 	var loaderID = 'club-loader';
    // 	var formID = 'bbps-club-form';
    // 	var btnID = 'bbps-club-btn';
    // }
    $("#" + btnID).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#" + loaderID).html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#" + formID).serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/payMasterBillAuth/" + service_id,
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#" + btnID).prop("disabled", false);
          $("#" + loaderID).html(data["msg"]);
          document.getElementById(formID).reset();
        } else {
          $("#" + btnID).prop("disabled", false);
          $("#" + loaderID).html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  }

  function updatedmrModel(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/master/getDMRCommData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateDMRModel").modal("show");
          $("#updateDMRBlock").html(data["str"]);
        } else {
          $("#updateDMRBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function updateaepsModel(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/master/getAEPSCommData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateDMRModel").modal("show");
          $("#updateDMRBlock").html(data["str"]);
        } else {
          $("#updateDMRBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function showOfferModal() {
    var siteUrl = $("#siteUrl").val();
    $("#offerModal").modal("show");
    $("#offerLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#offerFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getOperatorPlanList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#offerLoader").html(data["str"]);
        } else {
          $("#offerLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  }

  function showDTHOfferModal() {
    var siteUrl = $("#siteUrl").val();
    var cardNumber = $("#cardNumber").val();
    var operator = $("#operator").val();
    if (cardNumber == "" || operator == "") {
      $("#customerInfoLoader").html(
        '<font color="red">Please Select Operator and Card Number.</font>'
      );
    } else {
      $("#customerInfoLoader").html(
        "<center><img src='" +
        siteUrl +
        "skin/admin/images/large-loading.gif' width='100' /></center>"
      );
      var str = $("#admin_profile").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/recharge/getDTHOperatorPlanList",
        data: str,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#customerInfoLoader").html("");
            $("#customerName").val(data["customerName"]);
            $("#amount").val(data["monthlyRechargeAmount"]);
            $("#balanceInfo").html(
              "Available Balance - &#8377; " + data["balance"]
            );
          } else {
            $("#customerInfoLoader").html(
              '<font color="red">' + data["msg"] + "</font>"
            );
          }
        }
      });
    }
  }

  function offerAmountPick(amount) {
    $("#amount").val(amount);
    $("#offerModal").modal("hide");
  }

  function showROfferModal() {
    var siteUrl = $("#siteUrl").val();
    $("#rofferModal").modal("show");
    $("#rofferLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#rofferFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getRofferList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#rofferLoader").html(data["str"]);
        } else {
          $("#rofferLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  }

  function showDTHROfferModal() {
    var siteUrl = $("#siteUrl").val();
    $("#rofferModal").modal("show");
    var cardNumber = $("#cardNumber").val();
    $("#roffermobile").val(cardNumber);
    $("#rofferLoader").html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='200' /></center>"
    );
    var str = $("#rofferFilterForm").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/recharge/getDthOperatorPlanList",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#rofferLoader").html(data["str"]);
        } else {
          $("#rofferLoader").html(
            '<center><font color="red">' + data["msg"] + "</font></center>"
          );
        }
      }
    });
  }

  function offerAmountPick(amount) {
    $("#amount").val(amount);
    $("#offerModal").modal("hide");
    $("#DthOfferModal").modal("hide");
    $("#rofferModal").modal("hide");
  }
  function showComplainBox(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/recharge/getRechargeData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateComplainModel").modal("show");
          $("#complainRchgID").html(
            "<p><b>Recharge ID - " + data["txnid"] + "</b></p>"
          );
          $("#complainAmount").html(
            "<p><b>Amount - " + data["amount"] + "</b></p>"
          );
          $("#complainMsgBlock").html("");
        } else {
          $("#complainMsgBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function showBBPSComplainBox(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/recharge/getBBPSData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateComplainModel").modal("show");
          $("#complainRchgID").html(
            "<p><b>Recharge ID - " + data["txnid"] + "</b></p>"
          );
          $("#complainAmount").html(
            "<p><b>Amount - " + data["amount"] + "</b></p>"
          );
          $("#complainMsgBlock").html("");
        } else {
          $("#complainMsgBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function updateBenModel(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/transfer/getBenData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateDMRModel").modal("show");
          $("#updateDMRBlock").html(data["str"]);
        } else {
          $("#updateDMRBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function showAepsModal(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/aeps/getAepsData/" + id,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#updateDMRModel").modal("show");
          $("#updateDMRBlock").html(data["str"]);
        } else {
          $("#updateDMRBlock").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  }

  function dmtVerifyIfsc() {
    var siteUrl = $("#siteUrl").val();
    var ifsc = $("#ifsc").val();
    if (ifsc == "") {
      $(".ifsc-vefify-loader").html(
        '<font color="red">Please enter IFSC.</font>'
      );
    } else {
      $(".ifsc-vefify-loader").html(
        "<img src='" + siteUrl + "skin/admin/images/small-loading.gif' />"
      );
      $.ajax({
        type: "POST",
        url: siteUrl + "retailer/dmt/verifyIfscCode/" + ifsc,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $(".ifsc-vefify-loader").html(
              '<table class="table table-bordered"><tr><th colspan="5"><center>' +
              data["ifscDetails"] +
              "</center></th></tr><tr><th>Bank</th><th>Branch</th><th>City</th><th>District</th><th>State</th></tr><tr><th>" +
              data["bankName"] +
              "</th><th>" +
              data["branchName"] +
              "</th><th>" +
              data["city"] +
              "</th><th>" +
              data["district"] +
              "</th><th>" +
              data["state"] +
              '</th></tr><tr><th colspan="5">' +
              data["address"] +
              "</th></tr></table>"
            );
          } else {
            $(".ifsc-vefify-loader").html(
              '<font color="red">' + data["message"] + "</font>"
            );
          }
        }
      });
    }
  }

  //----- Money Transfer 2
  $("#accountVerifyBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var str = $("#account_verify_form").serialize();
    $('#loader').show();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bank/verifyAuth",
      data: str,
      dataType: "json",
      success: function (obj) {
        $('#loader').hide();
        $('.error').html('');
        $('#benAlert').removeClass('show').addClass('hide').html('');
        if (obj.error && obj.errors) {
          $.each(obj.errors, function (key, value) {
            $('#' + key + '_error').html(value);
          });
        } else if (obj.error) {
          $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger');
          if (obj.error && obj.apiresponse === "yes") {
            $('#benAlert').html(`<b><strong>Error : </strong>(${obj.dataval.statuscode} - ${obj.dataval.statuscodemessage})</b>`);
          } else {
            $('#benAlert').html(`<b><strong>Error : </strong>${obj.dataval}</b>`);
          }
          setTimeout(function () {
            $('#benAlert').removeClass('show').addClass('hide');
            $('#benAlert').empty();
          }, 5000);
        } else {
          $("#account_holder_name").val(obj.account_holder_name);
          $("#bankModal").modal("show");
          $("#bankResponse").html(obj.dataval);
        }
      },
      error: function (xhr, status, error) {
        $('#loader').hide();
        console.log("AJAX error: " + error);
        $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger').html("An error occurred. Please try again.");
      }
    });
  });

  $('#addBeneficiaryBtn').click(function (e) {
    e.preventDefault();

    // Serialize form data and prepare variables
    var formData = $('#verify_beneficary_addon_form').serialize();
    var dbTableName = $('#dbTableName').val();
    var actionUrl = "";
    var siteUrl = $('#siteUrl').val();
    const $button = $(this);
    const $spinner = $button.find('.spinner-border');

    if (dbTableName === "user_benificary") {
      actionUrl = siteUrl + "retailer/transfer/beneficiaryAuth";
    } else {
      actionUrl = siteUrl + "retailer/Settlement/beneficiaryAuth";
    }

    $spinner.show();
    $button.prop('disabled', true);
    $('.btnDisabled').prop('disabled', true);

    $.ajax({
      url: actionUrl,
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {


        if (response.error) {
          $spinner.hide();
          $button.prop('disabled', false);
          $('.btnDisabled').prop('disabled', false);
          $('.benAddonMsg').html(`<strong>Error: </strong>${response.dataval}`);
        } else {
          $('#verify_beneficary_addon_form')[0].reset();
          $('.benAddonMsg').html(`<strong>Success: </strong>${response.dataval}`);
          $button.prop('disabled', false);
          $('.btnDisabled').prop('disabled', false);
          setTimeout(function () {
            $('.benAddonMsg').html("");
            $('.benAddonMsg').empty();
            location.reload();
          }, 5000);
        }

      },
      error: function (xhr, status, error) {
        $spinner.hide();
        $button.prop('disabled', false);
        $('.btnDisabled').prop('disabled', false);
        console.log('AJAX Error: ' + error);
        $('.benAddonMsg').html(`<strong>Error: </strong>Failed to add beneficiary. Please try again.`);
        setTimeout(function () {
          $('.benAddonMsg').empty();
        }, 5000);
      }
    });
  });


  $("#upiVerifyBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".ajaxx-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/images/large-loading.gif' alt='loading' width='100' /></center>"
    );
    var str = $("#upi_verify_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bank/upiVerifyAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".ajaxx-loader").html("");
          $("#upi_account_holder_name").val(data["upi_account_holder_name"]);
          $("#bankUpiModal").modal("show");
          $("#bankUpiResponse").html(data["msg"]);
        } else {
          $(".ajaxx-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#selDmtBankID").change(function () {
    var siteUrl = $("#siteUrl").val();
    var billerID = $(this).val();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/dmt/getBankDefaultIfsc/" + billerID,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        $("#defaultIfscTxt").val(data["ifsc"]);
      }
    });
  });

  $("#is_default_ifsc").click(function () {
    if ($("#is_default_ifsc").is(":checked")) {
      $("#ifsc").val($("#defaultIfscTxt").val());
    } else {
      $("#ifsc").val("");
    }
  });

  function closeClubNoti(recordID = 0) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/saving/closeClubNotification/" + recordID,
      success: function (r) { }
    });
  }

  $("#to_bank").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/transfer/getBankBeneficiary",
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["str"] + "</font>"
          );
        }
      }
    });
  });

  $("#to_upi").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/transfer/getUpiBankBeneficiary",
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["str"] + "</font>"
          );
        }
      }
    });
  });

  $("#add_bank_account").click(function () {
    $("#show_bank_account").css("display", "block");
    $("#show_upi_account").css("display", "none");
  });

  $("#add_upi_account").click(function () {
    $("#show_upi_account").css("display", "block");
    $("#show_bank_account").css("display", "none");
  });

  $("#coupon").keyup(function () {
    var coupon = $("#coupon").val();

    if (coupon) {
      $.ajax({
        url: siteUrl + "retailer/pancard/getCouponBalance/" + coupon,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#amount").html(data["amount"]);
          } else {
            $("#amount").html(data["amount"]);
          }
        }
      });
    }
  });

  function payCreditCardBill(service_id) {
    var loaderID = "mobi-credit-card-loader";
    var formID = "bbps-credit-card-mobi-form";
    var btnID = "bbps-credit-card-mobi-btn";

    $("#" + btnID).prop("disabled", true);
    var siteUrl = $("#siteUrl").val();
    $("#" + loaderID).html(
      "<center><img src='" +
      siteUrl +
      "skin/admin/images/large-loading.gif' width='100' /></center>"
    );
    var str = $("#" + formID).serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/payCreditCardBillAuth/" + service_id,
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#" + btnID).prop("disabled", false);
          $("#" + loaderID).html(data["msg"]);
          document.getElementById(formID).reset();
        } else {
          $("#" + btnID).prop("disabled", false);
          $("#" + loaderID).html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  }

  function bbpsRecharge() {
    var siteUrl = $("#siteUrl").val();
    var str = $("#bbps_recharge").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bbps/mobilePrepaidAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          swal({
            title: data["msg"],
            icon: "success",
            button: "OK!"
          });

          setTimeout(function () {
            window.location.replace(siteUrl + "retailer/bbps");
          }, 10000);
        } else {
          swal({
            title: data["msg"],
            icon: "success",
            button: "OK!"
          });

          setTimeout(function () {
            window.location.replace(siteUrl + "retailer/bbps");
          }, 10000);
        }
      }
    });
  }

  $("#settlement_to_bank").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/settlement/getBankBeneficiary",
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["str"] + "</font>"
          );
        }
      }
    });
  });

  $("#settlement_to_upi").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "retailer/settlement/getUpiBankBeneficiary",
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["str"] + "</font>"
          );
        }
      }
    });
  });

  $("#settlement_add_bank_account").click(function () {
    $("#show_settlement_bank_account").css("display", "block");
    $("#show_settlement_upi_account").css("display", "none");
  });

  $("#settlement_add_upi_account").click(function () {
    $("#show_settlement_upi_account").css("display", "block");
    $("#show_settlement_bank_account").css("display", "none");
  });

  $("#settlementAccountVerifyBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".ajaxx-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/images/large-loading.gif' alt='loading' width='100' /></center>"
    );
    var str = $("#account_verify_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bank/openVerifyAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".ajaxx-loader").html("");
          $("#account_holder_name").val(data["account_holder_name"]);
          $("#account_ben_id").val(data["account_ben_id"]);
          $("#bankModal").modal("show");
          $("#bankResponse").html(data["msg"]);
        } else {
          $(".ajaxx-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $("#settlementUpiVerifyBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    $(".ajaxx-loader").html(
      "<center><img src='" +
      siteUrl +
      "skin/images/large-loading.gif' alt='loading' width='100' /></center>"
    );
    var str = $("#upi_verify_form").serialize();
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/bank/openUpiVerifyAuth",
      data: str,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".ajaxx-loader").html("");
          $("#upi_account_holder_name").val(data["upi_account_holder_name"]);
          $("#upi_ben_id").val(data["upi_ben_id"]);
          $("#bankUpiModal").modal("show");
          $("#bankUpiResponse").html(data["msg"]);
        } else {
          $(".ajaxx-loader").html(
            '<div class="alert alert-danger alert-dismissable">  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
            data["msg"] +
            "</div>"
          );
        }
      }
    });
  });

  $(document).ready(function () {
    function searchH4Value(searchTerm) {
      $(".master_list_col").hide();
      var firstChar = searchTerm.charAt(0).toUpperCase();
      var tail = searchTerm.substring(1);
      var formattedSearchTerm = firstChar + tail;

      $(".master_list_col").each(function () {
        var h4Text = $(this).find("h4").text();
        if (h4Text.toLowerCase().includes(formattedSearchTerm.toLowerCase())) {
          $(this).show();
        }
      });
    }

    $(".home_search").on("keyup", function () {
      var searchTerm = $(this).val().trim();
      if (searchTerm.length > 0) {
        searchH4Value(searchTerm);
      } else {
        $(".master_list_col").show();
      }
    });
  });

  $(document).ready(function () {

    $('.moneyTransferdiv').on('click', function (event) {
      event.preventDefault();
      $('#moneyTransferModel').modal('show');
    });

    $('.settlementMoneyTransferdiv').on('click', function (event) {
      event.preventDefault();
      $('#settlementMoneyTransferModel').modal('show');
    });

    $('.eKycModeldiv').on('click', function (event) {
      event.preventDefault();
      $('#eKycModel').modal('show');
    });
    //Add Bank M1 Benificary
    $('#saveMT1BeneficiaryBtn').click(function (e) {
      e.preventDefault();
      var formData = $('#account_verify_form').serialize();
      var siteUrl = $('#siteUrl').val();

      $.ajax({
        url: siteUrl + "retailer/transfer/beneficiaryAuth",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          // Clear previous errors and success messages
          $('.error').html('');
          $('#benAlert').removeClass('show').addClass('hide').html('');
          if (response.error && response.errors) {
            // Display validation errors
            $.each(response.errors, function (key, value) {
              $('#' + key + '_error').html(value);
            });
          } else if (response.error) {
            $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#benAlert').html(`<strong>Error: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#benAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#benAlert').html(`<strong>Success: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });
    });
    var delBeneficiaryId;
    $('.benm1Deletebtn').click(function (e) {
      e.preventDefault();
      delBeneficiaryId = $(this).attr('benm1DeleteID');
    });

    $('#confirmDeletem1').click(function () {
      var benDeleteUrl = siteUrl + `retailer/transfer/deleteBeneficiary/${delBeneficiaryId}`;
      $.ajax({
        url: benDeleteUrl,
        type: "POST",
        dataType: "json",
        success: function (response) {
          $('.error').html('');
          $('#benAlert').removeClass('show').addClass('hide').html('');
          $('#confirmModal').modal('hide');
          if (response.error) {
            $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#benAlert').html(`<strong>Error: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#benAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#benAlert').html(`<strong>Success: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });

    });

    //Update Ben Money Transfer 2 Benificary Bank Details :
    $('#saveBenM1Changes').click(function (e) {
      e.preventDefault();

      var formData = $('#updateBenM2BankData').serialize();
      var siteUrl = $('#siteUrl').val();

      $.ajax({
        url: siteUrl + "retailer/transfer/updateBenificaryAuth",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (obj) {

          if (obj.error && obj.errors) {
            $.each(obj.errors, function (key, value) {
              $('#' + key + '_error').html(value);
            });
          } else if (obj.error) {
            $('#updateBenAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#updateBenAlert').html(`<strong>Error: </strong>${obj.dataval}`);
            setTimeout(function () {
              $('#updateBenAlert').removeClass('show').addClass('hide');
              $('#updateBenAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#updateBenAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#updateBenAlert').html(`<strong>Success: </strong>${obj.dataval}`);
            setTimeout(function () {
              $('#updateBenAlert').removeClass('show').addClass('hide');
              $('#updateBenAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });
    });

    //Add Bank M2 Benificary
    $('#saveBeneficiaryBtn').click(function (e) {
      e.preventDefault();
      var formData = $('#account_verify_form').serialize();
      var siteUrl = $('#siteUrl').val();

      $.ajax({
        url: siteUrl + "retailer/settlement/beneficiaryAuth",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (response) {
          // Clear previous errors and success messages
          $('.error').html('');
          $('#benAlert').removeClass('show').addClass('hide').html('');
          if (response.error && response.errors) {
            // Display validation errors
            $.each(response.errors, function (key, value) {
              $('#' + key + '_error').html(value);
            });
          } else if (response.error) {
            $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#benAlert').html(`<strong>Error: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#benAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#benAlert').html(`<strong>Success: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });
    });
    var delBeneficiaryId;
    $('.benm2Deletebtn').click(function (e) {
      e.preventDefault();
      delBeneficiaryId = $(this).attr('benm2DeleteID');
    });

    $('#confirmDelete').click(function () {
      var benDeleteUrl = siteUrl + `retailer/settlement/deleteBeneficiary/${delBeneficiaryId}`;
      $.ajax({
        url: benDeleteUrl,
        type: "POST",
        dataType: "json",
        success: function (response) {
          $('.error').html('');
          $('#benAlert').removeClass('show').addClass('hide').html('');
          $('#confirmModal').modal('hide');
          if (response.error) {
            $('#benAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#benAlert').html(`<strong>Error: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#benAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#benAlert').html(`<strong>Success: </strong>${response.dataval}`);
            setTimeout(function () {
              $('#benAlert').removeClass('show').addClass('hide');
              $('#benAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });

    });

    //Update Ben Money Transfer 2 Benificary Bank Details :
    $('#saveBenM2Changes').click(function (e) {
      e.preventDefault();

      var formData = $('#updateBenM2BankData').serialize();
      var siteUrl = $('#siteUrl').val();

      $.ajax({
        url: siteUrl + "retailer/settlement/updateBenificaryAuth",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function (obj) {

          if (obj.error && obj.errors) {
            $.each(obj.errors, function (key, value) {
              $('#' + key + '_error').html(value);
            });
          } else if (obj.error) {
            $('#updateBenAlert').removeClass('hide alert-success').addClass('show alert-danger');
            $('#updateBenAlert').html(`<strong>Error: </strong>${obj.dataval}`);
            setTimeout(function () {
              $('#updateBenAlert').removeClass('show').addClass('hide');
              $('#updateBenAlert').empty();
            }, 3000);
          } else {
            $('#account_verify_form')[0].reset();
            $('#updateBenAlert').removeClass('hide alert-danger').addClass('show alert-success');
            $('#updateBenAlert').html(`<strong>Success: </strong>${obj.dataval}`);
            setTimeout(function () {
              $('#updateBenAlert').removeClass('show').addClass('hide');
              $('#updateBenAlert').empty();
              location.reload();
            }, 3000);
          }
        },
        error: function (xhr, status, error) {
          console.log('AJAX Error: ' + error);
        }
      });
    });
  });
  //------------- Money Transfer 2 --------
  function updateBenModel1(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/transfer/getBenData/" + id,
      success: function (response) {
        var data = JSON.parse($.trim(response));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateBenModel1").modal("show");
          $("#updatebenBlock1").html(data["dataval"]);
          return false;
        } else {
          $.each(response.errors, function (key, value) {
            $("#updateBenModel1").modal("show");
            $('#' + key + '_error').html(value);
          });
        }
      }
    });
  }

  function updateBenModel2(id) {
    var siteUrl = $("#siteUrl").val();
    $.ajax({
      url: siteUrl + "retailer/settlement/getBenData/" + id,
      success: function (response) {
        var data = JSON.parse($.trim(response));
        if (data["status"] == 1) {
          $("#recordID").val(id);
          $("#updateBenModel2").modal("show");
          $("#updatebenBlock2").html(data["dataval"]);
          return false;
        } else {
          $.each(response.errors, function (key, value) {
            $("#updateBenModel2").modal("show");
            $('#' + key + '_error').html(value);
          });
        }
      }
    });
  }
});

/**Activate AEPS 3  */
$(document).ready(function () {
  $(".aeps3btn").click(function (e) {
    e.preventDefault();
    var siteUrl = $("#siteUrl").val();
    var formData = new FormData($("#aeps3_form")[0]); // Collect form data, including files
    toggleWaitLoader(true);
    $.ajax({
      type: "POST",
      url: siteUrl + "retailer/iciciaeps/activeAuth",
      data: formData,
      dataType: "json",
      processData: false,
      cache: false,
      contentType: false,
      success: function (response) {
        // Hide loader
        toggleWaitLoader(false);
        // Reset error messages
        $('.error').html('');
        $('#aeps3Alert').removeClass('show alert-danger alert-success').addClass('hide').html('');

        if (response.error) {
          // Display validation errors
          if (response.errors) {
            $.each(response.errors, function (key, messages) {
              if (Array.isArray(messages)) {
                $('#' + key + '_error').html(messages.join('<br>'));
              } else if (messages) {
                $('#' + key + '_error').html(messages);
              }
            });
          }

          if (response.error && response.auth_errors) {
            $('#aeps3Alert').focus();
            $('#aeps3Alert')
              .removeClass('hide alert-success')
              .addClass('show alert-danger')
              .html(`<b><strong>Error:</strong> ${response.auth_errors}</b>`);
            setTimeout(function () {
              $('#aeps3Alert').removeClass('show').addClass('hide').empty();
            }, 5000);
          }

          // Display image-specific validation errors
          if (response.error && response.imageErrors) {
            $.each(response.imageErrors, function (key, messages) {
              if (Array.isArray(messages) && messages.length > 0) {
                $('#' + key + '_error').html(messages.join('<br>'));
              } else if (typeof messages === "string") {
                $('#' + key + '_error').html(messages);
              }
            });
          }
        } else {
          if (response.after_api_error) {
            $('#aeps3Alert').focus();
            $('#aeps3Alert')
              .removeClass('hide alert-success')
              .addClass('show alert-danger')
              .html(`<b><strong>Error:</strong> ${response.dataval}</b>`);
            setTimeout(function () {
              $('#aeps3Alert').removeClass('show').addClass('hide').empty();
            }, 5000);

          } else {
            $('#aeps3Alert').focus();
            $('#aeps3Alert')
              .addClass('hide alert-success')
              .removeClass('show alert-danger')
              .html(`<b><strong>Success:</strong> ${response.dataval}</b>`);
            setTimeout(function () {
              $('#aeps3Alert').removeClass('show').addClass('hide').empty();
              window.location.href = response.redirectUrl;
            }, 5000);
          }
        }
      },
      error: function (xhr, status, error) {
        toggleWaitLoader(false);
        console.error("AJAX error: " + error);
        $('#aeps3Alert')
          .removeClass('hide alert-success')
          .addClass('show alert-danger')
          .html("An error occurred. Please try again.");
      }
    });
  });
});


