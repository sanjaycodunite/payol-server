$(document).ready(function () {
  $("#changeAPISearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/api/getMemberActiveAPIData/" + memberID,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $(".recharge-comm-loader").html("");
          $("#recharge-comm-block").html(data["str"]);

          $("#check_all").click(function () {
            $('input[type="checkbox"]').prop("checked", this.checked);
          });
        } else {
          $(".recharge-comm-loader").html(
            '<font color="red">' + data["msg"] + "</font>"
          );
        }
      }
    });
  });

  $("#rechargeComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getRechargeCommData/" + memberID,
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

  $("#accountVerifyChargeSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url:
        siteUrl + "admin/master/getMemberAccountVerifyChargeData/" + memberID,
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

  $("#nsdlPancardChargeSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberNsdlPancardChargeData/" + memberID,
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

  $("#utiComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getUtiCommData/" + memberID,
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

  $("#upiComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getUpiCommData/" + memberID,
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

  $("#upiCashComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getUpiCashCommData/" + memberID,
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

  $("#bbpsComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getBBPSCommData/" + memberID,
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

  $("#bbpsLiveComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getBBPSLiveCommData/" + memberID,
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

  $("#dmrComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberDMRCommData/" + memberID,
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

  $("#moneyTransferComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberMoneyTransferCommData/" + memberID,
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

  $("#xpressPayoutChargeSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberXpressPayoutChargeData/" + memberID,
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

  $("#dmtChargeSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberDmtChargeData/" + memberID,
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

  $("#aepsComSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberAEPSCommData/" + memberID,
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

  $("#gatewayChargeSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/master/getMemberGatewayChargeData/" + memberID,
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
      url: siteUrl + "admin/master/getServiceData/" + memberID,
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

  $("#instantLoanSearchBtn").click(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html(
      "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
    );
    $.ajax({
      url: siteUrl + "admin/member/getIntantLoanData/" + memberID,
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
        url: siteUrl + "admin/aeps/getCityList/" + stateID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#selCity").html(data["str"]);
          }
        }
      });
    }
  });

  $("#selMemberType").change(function () {
    var siteUrl = $("#siteUrl").val();
    var memberType = $(this).val();
    if (memberType == 4 || memberType == 5) {
      var sponse =
        '<option value="0">Select Sponser Type</option><option value="3">Master Distributor</option><option value="4">Distributor</option>';
      if (memberType == 4) {
        sponse =
          '<option value="0">Select Sponser Type</option><option value="3">Master Distributor</option>';
      }

      $("#selSponserType").html(sponse);
      $("#selSponserType").selectpicker("refresh");

      $.ajax({
        url: siteUrl + "admin/master/getMemberTypeList/" + memberType,
        success: function (r) {
          $("#selMemberID").html(r);
          $("#selMemberID").selectpicker("refresh");
        }
      });
    }
  });

  $("#selSponserType").change(function () {
    var siteUrl = $("#siteUrl").val();
    var memberType = $(this).val();
    if (memberType == 4 || memberType == 3) {
      $.ajax({
        url: siteUrl + "admin/master/getMemberTypeList/" + memberType,
        success: function (r) {
          $("#selSponserID").html(r);
          $("#selSponserID").selectpicker("refresh");
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
        "<img src='" +
          siteUrl +
          "skin/front/images/loading2.gif' alt='loading' />"
      );
      $.ajax({
        type: "POST",
        url: siteUrl + "master/recharge/fetchBiller/" + operator_id,
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
        "<img src='" +
          siteUrl +
          "skin/front/images/loading2.gif' alt='loading' />"
      );
      var str = $("#electricity-form").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "master/recharge/fetchBillerDetail/" + operator_id,
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
        url: siteUrl + "admin/wallet/getMemberWalletBalance/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#balance").val(data["balance"]);
          }
        }
      });
    }
  });

  $("#selUpiWalletMember").change(function () {
    var siteUrl = $("#siteUrl").val();
    var memberID = $(this).val();
    if (memberID) {
      $.ajax({
        url: siteUrl + "admin/wallet/getMemberUpiWalletBalance/" + memberID,
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
        url: siteUrl + "admin/ewallet/getMemberWalletBalance/" + memberID,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          if (data["status"] == 1) {
            $("#balance").val(data["balance"]);
          }
        }
      });
    }
  });

  $("#response_type").change(function () {
    var response_type = $(this).val();
    if (response_type == 1) {
      $("#seperator_block").css("display", "block");
      $("#str_res_block").css("display", "block");
      $("#xml_res_block").css("display", "none");
      $("#json_res_block").css("display", "none");
    } else if (response_type == 2) {
      $("#seperator_block").css("display", "none");
      $("#str_res_block").css("display", "none");
      $("#xml_res_block").css("display", "block");
      $("#json_res_block").css("display", "none");
    } else if (response_type == 3) {
      $("#seperator_block").css("display", "none");
      $("#str_res_block").css("display", "none");
      $("#xml_res_block").css("display", "none");
      $("#json_res_block").css("display", "block");
    } else {
      $("#seperator_block").css("display", "none");
      $("#str_res_block").css("display", "none");
      $("#xml_res_block").css("display", "none");
      $("#json_res_block").css("display", "none");
    }
  });

  $("#get_balance_response_type").change(function () {
    var response_type = $(this).val();
    if (response_type == 1) {
      $("#get_balance_seperator_block").css("display", "block");
      $("#get_balance_str_res_block").css("display", "block");
      $("#get_balance_xml_res_block").css("display", "none");
      $("#get_balance_json_res_block").css("display", "none");
    } else if (response_type == 2) {
      $("#get_balance_seperator_block").css("display", "none");
      $("#get_balance_str_res_block").css("display", "none");
      $("#get_balance_xml_res_block").css("display", "block");
      $("#get_balance_json_res_block").css("display", "none");
    } else if (response_type == 3) {
      $("#get_balance_seperator_block").css("display", "none");
      $("#get_balance_str_res_block").css("display", "none");
      $("#get_balance_xml_res_block").css("display", "none");
      $("#get_balance_json_res_block").css("display", "block");
    } else {
      $("#get_balance_seperator_block").css("display", "none");
      $("#get_balance_str_res_block").css("display", "none");
      $("#get_balance_xml_res_block").css("display", "none");
      $("#get_balance_json_res_block").css("display", "none");
    }
  });

  $("#check_status_response_type").change(function () {
    var response_type = $(this).val();
    if (response_type == 1) {
      $("#check_status_seperator_block").css("display", "block");
      $("#check_status_str_res_block").css("display", "block");
      $("#check_status_xml_res_block").css("display", "none");
      $("#check_status_json_res_block").css("display", "none");
    } else if (response_type == 2) {
      $("#check_status_seperator_block").css("display", "none");
      $("#check_status_str_res_block").css("display", "none");
      $("#check_status_xml_res_block").css("display", "block");
      $("#check_status_json_res_block").css("display", "none");
    } else if (response_type == 3) {
      $("#check_status_seperator_block").css("display", "none");
      $("#check_status_str_res_block").css("display", "none");
      $("#check_status_xml_res_block").css("display", "none");
      $("#check_status_json_res_block").css("display", "block");
    } else {
      $("#check_status_seperator_block").css("display", "none");
      $("#check_status_str_res_block").css("display", "none");
      $("#check_status_xml_res_block").css("display", "none");
      $("#check_status_json_res_block").css("display", "none");
    }
  });
});
function showStrResponseStatus(id, val) {
  if (val == 2) {
    $("#str_res_status_" + id).css("display", "block");
  } else {
    $("#str_res_status_" + id).css("display", "none");
  }
}
function showXMLResponseStatus(id, val) {
  if (val == 2) {
    $("#xml_res_status_" + id).css("display", "block");
  } else {
    $("#xml_res_status_" + id).css("display", "none");
  }
}
function showJsonResponseStatus(id, val) {
  if (val == 2) {
    $("#json_res_status_" + id).css("display", "block");
  } else {
    $("#json_res_status_" + id).css("display", "none");
  }
}

function showGetBalanceStrResponseStatus(id, val) {
  if (val == 2) {
    $("#get_balance_str_res_status_" + id).css("display", "block");
  } else {
    $("#get_balance_str_res_status_" + id).css("display", "none");
  }
}
function showGetBalanceXMLResponseStatus(id, val) {
  if (val == 2) {
    $("#get_balance_xml_res_status_" + id).css("display", "block");
  } else {
    $("#get_balance_xml_res_status_" + id).css("display", "none");
  }
}
function showGetBalanceJsonResponseStatus(id, val) {
  if (val == 2) {
    $("#get_balance_json_res_status_" + id).css("display", "block");
  } else {
    $("#get_balance_json_res_status_" + id).css("display", "none");
  }
}

function showCheckStatusStrResponseStatus(id, val) {
  if (val == 2) {
    $("#check_status_str_res_status_" + id).css("display", "block");
  } else {
    $("#check_status_str_res_status_" + id).css("display", "none");
  }
}
function showCheckStatusXMLResponseStatus(id, val) {
  if (val == 2) {
    $("#check_status_xml_res_status_" + id).css("display", "block");
  } else {
    $("#check_status_xml_res_status_" + id).css("display", "none");
  }
}
function showCheckStatusJsonResponseStatus(id, val) {
  if (val == 2) {
    $("#check_status_json_res_status_" + id).css("display", "block");
  } else {
    $("#check_status_json_res_status_" + id).css("display", "none");
  }
}

function showCallbackResponseStatus(id, val) {
  if (val == 2) {
    $("#call_back_res_status_" + id).css("display", "block");
  } else {
    $("#call_back_res_status_" + id).css("display", "none");
  }
}
function updatedmrModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getDMRCommData/" + id,
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

function updateMoneyTransferModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getMoneyTransferCommData/" + id,
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

function updateXpressPayoutChargeModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getXpressPayoutChargeData/" + id,
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

function updateDmtChargeModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getDmtChargeData/" + id,
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

function updateAmountFilterModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/api/getAmountFilterData/" + id,
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
    url: siteUrl + "admin/master/getAEPSCommData/" + id,
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

function updateGatewayModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getGatewayChargeData/" + id,
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
    url: siteUrl + "admin/report/getAepsData/" + id,
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

function successPayout(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getPayoutData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function clubChatModal(round_no, club_id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/society/getClubChatList/" + round_no + "/" + club_id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $(".modal-title").html("View Chat for Round #" + round_no);
        $("#updateComplainModel").modal("show");
        $("#club-live-member-block").html(data["str"]);
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function successUpiPayout(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getUpiPayoutData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function successNewPayout(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getNewPayoutData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function successMoneyTranfer(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getMoneyTransferData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function updateAccountVerifyChargeModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getAccountVerifyChargeData/" + id,
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

function updateNsdlPancardChargeModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getNsdlPancardChargeData/" + id,
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

$("#panActivationSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getPanActivationCommData/" + memberID,
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

$("#findPanSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getFindPanCommData/" + memberID,
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

$("#panSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getPanCommData/" + memberID,
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

function uploadPanImg(val) {
  $("#aadharID").val(val);
  $("#findPanModal").modal("show");
}

function updateUpiQrModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getUpiQrCommData/" + id,
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

function checkUpload(val) {
  $("#pan_div").hide();
  $("#aadhar_front_div").hide();
  $("#aadhar_back_div").hide();

  if (val == "PAN") {
    $("#pan_div").show();
  } else if (val == "AADHAAR") {
    $("#aadhar_front_div").show();
    $("#aadhar_back_div").show();
  }
}

$("#utiBalanceSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getUtiBalanceCommData/" + memberID,
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

function utiBalanceBox(id) {
  $("#requestID").val(id);
  $("#updateUtiBalanceModel").modal("show");
}

$("#myTable").on("input", ".amount", function () {
  var calculated_total_amount_sum = 0;
  var calculated_total_taxable_amount_sum = 0;
  var calculated_total_tax_amount_sum = 0;

  $("#myTable .amount").each(function () {
    var get_textbox_value = $(this).val();
    if ($.isNumeric(get_textbox_value)) {
      calculated_total_amount_sum += parseFloat(get_textbox_value);
    }
  });

  $("#total_sum_amount").val(calculated_total_amount_sum);
});

$("#myTable").on("input", ".taxable_amount", function () {
  var calculated_total_taxable_amount_sum = 0;

  $("#myTable .taxable_amount").each(function () {
    var get_textbox_value = $(this).val();
    if ($.isNumeric(get_textbox_value)) {
      calculated_total_taxable_amount_sum += parseFloat(get_textbox_value);
    }
  });

  $("#total_taxable_amount").val(calculated_total_taxable_amount_sum);
});

$("#myTable").on("input", ".tax_amount", function () {
  var calculated_total_tax_amount_sum = 0;

  $("#myTable .tax_amount").each(function () {
    var get_textbox_value = $(this).val();
    if ($.isNumeric(get_textbox_value)) {
      calculated_total_tax_amount_sum += parseFloat(get_textbox_value);
    }
  });

  $("#total_tax_amount").val(calculated_total_tax_amount_sum);
});

$("#surcharge, #amount, #surcharge_type").change(function () {
  var surcharge = parseFloat($("#surcharge").val());
  var amount = parseFloat($("#amount").val());
  var surcharge_typeID = parseInt($("#surcharge_type").val());

  if (!isNaN(amount) && !isNaN(surcharge)) {
    if (surcharge_typeID === 0) {
      var total = amount - surcharge;
      $("#final_amount").val(total);
    }
    // Assuming '1' represents Percentage (%)
    else if (surcharge_typeID === 1) {
      var total = (amount * surcharge) / 100;
      var final = amount - total;
      $("#final_amount").val(final);
    } else {
      var final = amount;
      $("#final_amount").val(final);
    }
  } else {
  }
});

$("#upiSwitchSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getUpiApiData/" + memberID,
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

function releaseUpiAmount(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/releaseUpiAmount/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));

      if (data["status"] == 1) {
        Swal.fire({
          icon: "success",
          title: data["msg"],
          timer: 2000
        });
        $("#releaseUpiBalanceReportDataTable")
          .DataTable()
          .ajax.reload(null, false);
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: data["msg"]
        });
        $("#releaseUpiBalanceReportDataTable")
          .DataTable()
          .ajax.reload(null, false);
      }
    }
  });
}

$("#user_type").change(function () {
  var siteUrl = $("#siteUrl").val();
  var userType = $(this).val();
  if (userType) {
    $.ajax({
      url: siteUrl + "admin/report/getUserList/" + userType,
      success: function (r) {
        var data = JSON.parse($.trim(r));
        if (data["status"] == 1) {
          $("#user").html(data["str"]);
        }
      }
    });
  }
});

function updateScanPayModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getUpiScanPayCommData/" + id,
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

$("#scanPaySearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getScanPayCommData/" + memberID,
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

$("#openMoneyPayoutBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getMemberOpenMoneyCommData/" + memberID,
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

function updateOpenMoneyPayout(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getOpenMoneyPayoutCommData/" + id,
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

function successOpenMoneyPayout(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getOpenMoneyPayoutData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

$("#newMoneyTransferBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getMemberNewMoneyTransferCommData/" + memberID,
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

function updateNewMoneyTransferCharge(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getNewMoneyTransferCommData/" + id,
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

function successSettlementMoneyTransfer(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getSettlementMoneyTransferData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function successScanPay(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getScanPayData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainRchgID").html(
          "<p><b>Txn ID - " + data["txnid"] + "</b></p>"
        );
        $("#complainAmount").html(
          "<p><b>Amount - " + data["amount"] + "</b></p>"
        );
        $("#complainMsgBlock").html("");
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

$("#addFundComSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getAddFundCommData/" + memberID,
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

function updateAddFundModel(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/master/getAddFundMemberCommData/" + id,
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

$("#aepsTranscationSearchBtn").click(function () {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#selMemberID").val();
  $(".recharge-comm-loader").html(
    "<img src='" + siteUrl + "skin/images/loading2.gif' alt='loading' />"
  );
  $.ajax({
    url: siteUrl + "admin/master/getAepsTranscationCommData/" + memberID,
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

function showUtrModal(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/getUtrData/" + id,
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

function approveMemberRequest(id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/member/getMemberRequestData/" + id,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(data["data"]);
      } else {
        $("#recordID").val(id);
        $("#updateComplainModel").modal("show");
        $("#complainMsgBlock").html(
          '<font color="red">' + data["msg"] + "</font>"
        );
      }
    }
  });
}

function updateMemberRequest(member_id, role_id) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    type: "POST",
    url: siteUrl + "admin/member/saveRequestMember",
    data: { member_id: member_id, role_id: role_id },
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        Swal.fire({
          title: data["msg"],
          icon: "success",
          button: "OK!"
        });

        location.reload();
      } else {
        Swal.fire({
          title: data["msg"],
          icon: "error",
          button: "OK!"
        });
        location.reload();
      }
    }
  });
}

/*** Add on code for get users according to the user_type */

$(document).ready(function() {
    $('#user_type').on('change', function(e) {
        e.preventDefault(); 
        var userType = $(this).val();
        $('#memberWise #user').empty();
        
        $.ajax({
            type: "POST",
            url: siteUrl + "admin/report/getUserByType",
            data: { userType: userType },
            success: function(response) {
				$('#memberWise #user').empty();
                var results = JSON.parse($.trim(response));
                if (results.status == 1) {
                    $('#memberWise #user').html(results.data);
				
                } else {
                    $('#memberWise #user').html(results.defaultUsers);
                }
                
				if ($('#memberWise #user').hasClass('selectpicker')) {
                    $('#memberWise #user').selectpicker('refresh');
					$('#memberWise #user').selectpicker('refresh');
                }
			}
        });
    });
});

