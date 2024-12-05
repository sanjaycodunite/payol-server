$("#example").DataTable();
$("#example1").DataTable();
dashboardApiDataTable();
employeDataTable();
deactiveMemberDataTable();
downlineDataTable();
uplineDataTable();
mdMemberDataTable();
distributorMemberDataTable();
retailerMemberDataTable();
apiMemberDataTable();
userDataTable();
walletDataTable();
upiWalletHistoryDataTable();
oldWalletDataTable();
creditDataTable();
debitDataTable();
fundRequestDataTable();
myWalletDataTable();
clubDataTable();
myFundRequestDataTable();
iciciAepsKycDataTable();
tdsDataTable();
myEwalletDataTable();
eWalletDataTable();
cWalletDataTable();
vanTxnDataTable();
dynamicInvoiceDataTable();
invoiceSummeryDataTable();
accountDataTable();
iciciAccountDataTable();
rechargeDataTable();
balanceReportDataTable();
upiBalanceReportDataTable();
releaseUpiBalanceReportDataTable();
commissionReportDataTable();
bbpsDataTable();
aepsKycDataTable();
aepsHistoryDataTable();
aepsReconDataTable();
iciciAepsHistoryDataTable();
matmHistoryDataTable();
gatewayHistoryDataTable();
manualMoneyTransferDataTable();
adminBankTransferDataTable();
moneyTransferDataTable();
moneyTransferHistoryDataTable();
newMoneyTransferHistoryDataTable();
upiTransferHistoryDataTable();
apiDataTable();
ticketDataTable();
rechargeCommisionDataTable();
bbpsCommisionDataTable();
fundTransferCommisionDataTable();
moneyTransferCommisionDataTable();
openPayoutCommisionDataTable();
aepsCommisionDataTable();
myAepsCommisionDataTable();
cashDepositeCommisionDataTable();
upiCommisionDataTable();
referralCommisionDataTable();
upiCashCommisionDataTable();
moveMemberDataTable();

complainDataTable();
bbpsHistoryDataTable();
topupHistoryDataTable();
walletDeductDataTable();
retailerQrDataTable();
upiWalletDataTable();
upiDataTable();
upiQrDataTable();
upiApiLogDataTable();
payoutApiLogDataTable();
upiCashDataTable();
utiPancardDataTable();
currentAccountDataTable();
axisAccountDataTable();
cashDepositeDataTable();
upiCollectionQrDataTable();
upiCashQrDataTable();
comWalletDataTable();
dmtHistoryDataTable();
nsdlListDataTable();
nsdlPanListDataTable();
nsdlActivationDataTable();
nsdlPanCardListDataTable();
bomListDataTable();
newAepsKycDataTable();
newAepsHistoryDataTable();
newPayoutTransferDataTable();
gstDataTable();
findPanDataTable();
utiPanRequestDataTable();
tdsInvoiceDataTable();
tdsInvoiceSummeryDataTable();
manualInvoiceDataTable();
apiFundRequestDataTable();
scanPayTransferHistoryDataTable();
upiChargebackDataTable();
openMoneyTransferHistoryDataTable();
openMoneyUpiTransferHistoryDataTable();
settlementMoneyTransferHistoryDataTable();
addFundDataTable();
newMoneyTransferHistoryOldDataTable();
memberRequestDataTable();
kycDataTable();
aepsApiLogDataTable();

function newPayoutTransferDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = ""
) {
  var siteUrl = $("#siteUrl").val();

  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  var newPayoutTransferDataTable = $("#newPayoutTransferDataTable").DataTable({
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[9, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNewPayoutTransferList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + fromDate + "|" + toDate + "|" + status + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#newPayoutTransferSearchBtn").on("click", function () {
  $("#newPayoutTransferDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  newPayoutTransferDataTable(keyword, fromDate, toDate, status, user);
});




function upiWalletDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var upiWalletDataTable = $("#upiWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiWalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#upiWalletSearchBtn").on("click", function () {
  $("#upiWalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var date = $("#date").val();
  upiWalletDataTable(keyword, member_id, date);
});

function nsdlListDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var nsdlListDataTable = $("#nsdlListDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNsdlList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#nsdlListSearchBtn").on("click", function () {
  $("#nsdlListDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  nsdlListDataTable(keyword, fromDate, toDate);
});

function nsdlPanListDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var nsdlPanListDataTable = $("#nsdlPanListDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNsdlPanList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#nsdlPanListSearchBtn").on("click", function () {
  $("#nsdlPanListDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  nsdlPanListDataTable(keyword, fromDate, toDate);
});

function bomListDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var bomListDataTable = $("#bomListDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getBomList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#bomListSearchBtn").on("click", function () {
  $("#bomListDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  bomListDataTable(keyword, fromDate, toDate);
});

function comWalletDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var comWalletDataTable = $("#comWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getWalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#comWalletSearchBtn").on("click", function () {
  $("#comWalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var date = $("#date").val();
  comWalletDataTable(keyword, member_id, date);
});

function cashDepositeDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var cashDepositeDataTable = $("#cashDepositeDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getCashDepositeList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#cashDepositeSearchBtn").on("click", function () {
  $("#cashDepositeDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  cashDepositeDataTable(keyword, date);
});

function upiCollectionQrDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var upiCollectionQrDataTable = $("#upiCollectionQrDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getQRList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function upiCashQrDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var upiCashQrDataTable = $("#upiCashQrDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getCashQRList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function currentAccountDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var currentAccountDataTable = $("#currentAccountDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getCurrentAccountList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#currentAccountSearchBtn").on("click", function () {
  $("#currentAccountDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  currentAccountDataTable(keyword, fromDate, toDate);
});

function axisAccountDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var axisAccountDataTable = $("#axisAccountDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getAxisAccountList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#axisAccountSearchBtn").on("click", function () {
  $("#axisAccountDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  axisAccountDataTable(keyword, fromDate, toDate);
});

function retailerQrDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var retailerQrDataTable = $("#retailerQrDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getQrRetailerList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#retailerQrSearchBtn").on("click", function () {
  $("#retailerQrDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  retailerQrDataTable(keyword, date);
});

function dashboardApiDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var dashboardApiDataTable = $("#dashboardApiDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    bPaginate: false,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    deferRender: true,
    ajax: {
      url: "dashboard/getAPIBalanceData",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });

  /*setInterval( function () {
       dashboardApiDataTable.ajax.reload();
   }, 60000 );*/
}

function topupHistoryDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var topupHistoryDataTable = $("#topupHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getTopupHistory",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function employeDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var employeDataTable = $("#employeDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#employeSearchBtn").on("click", function () {
  $("#employeDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  employeDataTable(keyword, date);
});

function deactiveMemberDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var deactiveMemberDataTable = $("#deactiveMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getDeactiveMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#deactiveMemberSearchBtn").on("click", function () {
  $("#deactiveMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  deactiveMemberDataTable(keyword, date);
});

function downlineDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#memberID").val();
  var downlineDataTable = $("#downlineDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[1, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/member/getDownlineMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date + "|" + memberID;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function uplineDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var memberID = $("#memberID").val();
  var uplineDataTable = $("#uplineDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[1, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/member/getUplineMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date + "|" + memberID;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function aepsKycDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var aepsKycDataTable = $("#aepsKycDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getAepsKycList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#aepsKycSearchBtn").on("click", function () {
  $("#aepsKycDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  aepsKycDataTable(keyword, fromDate, toDate, user);
});

function iciciAepsKycDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  user = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var iciciAepsKycDataTable = $("#iciciAepsKycDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getIciciAepsKycList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#iciciAepsKycSearchBtn").on("click", function () {
  $("#iciciAepsKycDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  iciciAepsKycDataTable(keyword, fromDate, toDate, user);
});

function tdsDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (user == "") {
    var user = $("#user").val();
  }
  var tdsDataTable = $("#tdsDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getTdsReportList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessTdsBlock").html(json.successAmount);
    }
  });
}

$("#tdsSearchBtn").on("click", function () {
  $("#tdsDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  tdsDataTable(keyword, fromDate, toDate, user);
});

function walletDeductDataTable(wallet_type = "", user_type = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var walletDeductDataTable = $("#walletDeductDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getWalletDeductList",
      data: function (d) {
        d.extra_search = wallet_type + "|" + user_type + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#walletDeductSearchBtn").on("click", function () {
  $("#walletDeductDataTable").DataTable().destroy();
  var wallet_type = $("#wallet_type").val();
  var user_type = $("#user_type").val();
  var date = $("#date").val();
  walletDeductDataTable(wallet_type, user_type, date);
});

function aepsHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  service = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (service == "") {
    var service = $("#service").val();
  }

  var siteUrl = $("#siteUrl").val();
  var aepsHistoryDataTable = $("#aepsHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getFingpayAepsHistoryList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          service;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessAepsBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
      $("#totalFailedAepsBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#aepsHistorySearchBtn").on("click", function () {
  $("#aepsHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var service = $("#service").val();
  aepsHistoryDataTable(keyword, fromDate, toDate, status, user, service);
});

function aepsReconDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  service = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (service == "") {
    var service = $("#service").val();
  }

  var siteUrl = $("#siteUrl").val();
  var aepsReconDataTable = $("#aepsReconDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      },
      {
        targets: 14,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getFingpayReconList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          service;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessAepsBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
      $("#totalFailedAepsBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#aepsReconSearchBtn").on("click", function () {
  $("#aepsReconDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var service = $("#service").val();
  aepsReconDataTable(keyword, fromDate, toDate, status, user, service);
});

function iciciAepsHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  service = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (user == "") {
    var user = $("#user").val();
  }

  if (service == "") {
    var service = $("#service").val();
  }

  var siteUrl = $("#siteUrl").val();
  var iciciAepsHistoryDataTable = $("#iciciAepsHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getIciciAepsHistoryList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          service;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessAepsBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
      $("#totalFailedAepsBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#iciciAepsHistorySearchBtn").on("click", function () {
  $("#iciciAepsHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var service = $("#service").val();
  iciciAepsHistoryDataTable(keyword, fromDate, toDate, status, user, service);
});

function matmHistoryDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var matmHistoryDataTable = $("#matmHistoryDataTable").DataTable({
    dom: "lBrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [
      [10, 25, 50, 4294967295],
      [10, 25, 50, "All"]
    ],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMatmHistoryList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#matmHistorySearchBtn").on("click", function () {
  $("#matmHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  matmHistoryDataTable(keyword, fromDate, toDate);
});

function gatewayHistoryDataTable(keyword = "", fromDate = "", toDate = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var gatewayHistoryDataTable = $("#gatewayHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getTopupHistory",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#gatewayHistorySearchBtn").on("click", function () {
  $("#gatewayHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  gatewayHistoryDataTable(keyword, fromDate, toDate);
});

function apiDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var apiDataTable = $("#apiDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getAPIList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

function mdMemberDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var mdMemberDataTable = $("#mdMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMDMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#mdMemberSearchBtn").on("click", function () {
  $("#mdMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  mdMemberDataTable(keyword, date);
});

function distributorMemberDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var distributorMemberDataTable = $("#distributorMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getDistributorList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#distributorMemberSearchBtn").on("click", function () {
  $("#distributorMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  distributorMemberDataTable(keyword, date);
});

function retailerMemberDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var retailerMemberDataTable = $("#retailerMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getRetailerList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#retailerMemberSearchBtn").on("click", function () {
  $("#retailerMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  retailerMemberDataTable(keyword, date);
});

function apiMemberDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var apiMemberDataTable = $("#apiMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getApiMemberList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#apiMemberSearchBtn").on("click", function () {
  $("#apiMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  apiMemberDataTable(keyword, date);
});

function userDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var userDataTable = $("#userDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUserList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#userSearchBtn").on("click", function () {
  $("#userDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  userDataTable(keyword, date);
});

function walletDataTable(
  keyword = "",
  member_id = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var walletDataTable = $("#walletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getwalletList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + member_id + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.totalCreditAmount + " / " + json.totalCreditRecord
      );
      $("#totalPendingIciciBlock").html(
        json.totalDebitAmount + " / " + json.totalDebitRecord
      );
    }
  });
}
$("#walletSearchBtn").on("click", function () {
  $("#walletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  walletDataTable(keyword, member_id, fromDate, toDate);
});

function upiWalletHistoryDataTable(
  keyword = "",
  member_id = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var upiWalletHistoryDataTable = $("#upiWalletHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiWalletList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + member_id + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.totalCreditAmount + " / " + json.totalCreditRecord
      );
      $("#totalPendingIciciBlock").html(
        json.totalDebitAmount + " / " + json.totalDebitRecord
      );
    }
  });
}
$("#upiWalletHistorySearchBtn").on("click", function () {
  $("#upiWalletHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  upiWalletHistoryDataTable(keyword, member_id, fromDate, toDate);
});

function oldWalletDataTable(
  status = 0,
  keyword = "",
  member_id = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }

  if (status == 0) {
    var status = $("#status").val();
  }

  var siteUrl = $("#siteUrl").val();
  var oldWalletDataTable = $("#oldWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getOldWalletList",
      data: function (d) {
        d.extra_search =
          status +
          "|" +
          keyword +
          "|" +
          member_id +
          "|" +
          fromDate +
          "|" +
          toDate;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.totalCreditAmount + " / " + json.totalCreditRecord
      );
      $("#totalPendingIciciBlock").html(
        json.totalDebitAmount + " / " + json.totalDebitRecord
      );
    }
  });
}
$("#oldWalletSearchBtn").on("click", function () {
  $("#oldWalletDataTable").DataTable().destroy();
  var status = $("#status").val();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  oldWalletDataTable(status, keyword, member_id, fromDate, toDate);
});

function eWalletDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var eWalletDataTable = $("#eWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getwalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#eWalletSearchBtn").on("click", function () {
  $("#eWalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var date = $("#date").val();
  eWalletDataTable(keyword, member_id, date);
});

function cWalletDataTable(keyword = "", fromDate = "", toDate = "") {
  var siteUrl = $("#siteUrl").val();
  var cWalletDataTable = $("#cWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["copy", "csv", "excel", "pdf"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getWalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#cWalletSearchBtn").on("click", function () {
  $("#cWalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  cWalletDataTable(keyword, fromDate, toDate);
});

function vanTxnDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var vanTxnDataTable = $("#vanTxnDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["copy", "csv", "excel", "pdf"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getVanTxnList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalSuccess);
    }
  });
}
$("#vanTxnSearchBtn").on("click", function () {
  $("#vanTxnDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  vanTxnDataTable(keyword, fromDate, toDate, user);
});

function ticketDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var ticketDataTable = $("#ticketDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getTicketList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#ticketSearchBtn").on("click", function () {
  $("#ticketDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  ticketDataTable(keyword);
});

function complainDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var complainDataTable = $("#complainDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/complain/getComplainList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#complainSearchBtn").on("click", function () {
  $("#complainDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  complainDataTable(keyword);
});

function creditDataTable(
  keyword = "",
  member_id = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }

  var siteUrl = $("#siteUrl").val();
  var creditDataTable = $("#creditDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getcreditList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + member_id + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.totalCreditAmount + " / " + json.totalCreditRecord
      );
    }
  });
}

$("#creditSearchBtn").on("click", function () {
  $("#creditDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  creditDataTable(keyword, member_id, fromDate, toDate);
});

function debitDataTable(
  keyword = "",
  member_id = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }

  var siteUrl = $("#siteUrl").val();
  var debitDataTable = $("#debitDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getdebitList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + member_id + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {
      $("#totalPendingIciciBlock").html(
        json.totalDebitAmount + " / " + json.totalDebitRecord
      );
    }
  });
}

$("#debitSearchBtn").on("click", function () {
  $("#debitDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  debitDataTable(keyword, member_id, fromDate, toDate);
});

function fundRequestDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var fundRequestDataTable = $("#fundRequestDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getRequestList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#searchRequestBtn").on("click", function () {
  $("#fundRequestDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  fundRequestDataTable(keyword, date);
});

function myWalletDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var myWalletDataTable = $("#myWalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMyWalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#myWalletSearchBtn").on("click", function () {
  $("#myWalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var date = $("#date").val();
  myWalletDataTable(keyword, member_id, date);
});

function clubDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var clubDataTable = $("#clubDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[1, "desc"]],
    deferRender: true,
    ajax: {
      url: "society/getClubList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#clubSearchBtn").on("click", function () {
  $("#clubDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  clubDataTable(keyword);
});

function myEwalletDataTable(keyword = "", member_id = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var myEwalletDataTable = $("#myEwalletDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMyWalletList",
      data: function (d) {
        d.extra_search = keyword + "|" + member_id + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#myEwalletSearchBtn").on("click", function () {
  $("#myEwalletDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var member_id = $("#member_id").val();
  var date = $("#date").val();
  myEwalletDataTable(keyword, member_id, date);
});

function myFundRequestDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var myFundRequestDataTable = $("#myFundRequestDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMyRequestList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#searchMyRequestBtn").on("click", function () {
  $("#myFundRequestDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  myFundRequestDataTable(keyword, date);
});

function rechargeDataTable(
  status = 0,
  keyword = "",
  fromDate = "",
  toDate = "",
  user_type = "",
  operator = ""
) {
  if (status == 0) {
    var status = $("#status").val();
  }
  if (fromDate == "" && toDate == "") {
    var fromDate = $("#from_date").val();
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var rechargeDataTable = $("#rechargeDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      },
      {
        targets: 14,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getRechargeList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user_type +
          "|" +
          operator;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingRechargeBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedRechargeBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });

  /* setInterval( function () {
       rechargeDataTable.ajax.reload();
   }, 60000 );*/
}

$("#rechargeSearchBtn").on("click", function () {
  $("#rechargeDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user_type = $("#user_type").val();
  var operator = $("#operator").val();
  rechargeDataTable(status, keyword, fromDate, toDate, user_type, operator);
});

function bbpsHistoryDataTable(
  status = 0,
  keyword = "",
  fromDate = "",
  toDate = "",
  user_type = "",
  user = ""
) {
  if (status == 0) {
    var status = $("#status").val();
  }
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var bbpsHistoryDataTable = $("#bbpsHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getBbpsHistoryList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user_type +
          "|" +
          user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessBBPSBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingBBPSBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedBBPSBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });

  /* setInterval( function () {
       rechargeDataTable.ajax.reload();
   }, 60000 );*/
}

$("#bbpsHistorySearchBtn").on("click", function () {
  $("#bbpsHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user_type = $("#user_type").val();
  var user = $("#user").val();
  bbpsHistoryDataTable(status, keyword, fromDate, toDate, user_type, user);
});

function balanceReportDataTable(keyword = "", user_type = "") {
  var siteUrl = $("#siteUrl").val();
  var balanceReportDataTable = $("#balanceReportDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getBalanceReport",
      data: function (d) {
        d.extra_search = keyword + "|" + user_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalBalanceBlock").html(json.total_wallet_balance);
      $("#totalActualBalanceBlock").html(json.total_actual_balance);
    }
  });
}

$("#balanceReportSearchBtn").on("click", function () {
  $("#balanceReportDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var user_type = $("#user_type").val();
  balanceReportDataTable(keyword, user_type);
});

function upiBalanceReportDataTable(keyword = "", user_type = "") {
  var siteUrl = $("#siteUrl").val();
  var upiBalanceReportDataTable = $("#upiBalanceReportDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getUpiBalanceReport",
      data: function (d) {
        d.extra_search = keyword + "|" + user_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalBalanceBlock").html(json.total_wallet_balance);
      $("#totalActualBalanceBlock").html(json.total_actual_balance);
    }
  });
}

$("#upiBalanceReportSearchBtn").on("click", function () {
  $("#upiBalanceReportDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var user_type = $("#user_type").val();
  upiBalanceReportDataTable(keyword, user_type);
});

function releaseUpiBalanceReportDataTable(keyword = "", user_type = "") {
  var siteUrl = $("#siteUrl").val();
  var releaseUpiBalanceReportDataTable = $(
    "#releaseUpiBalanceReportDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getReleaseUpiBalanceReport",
      data: function (d) {
        d.extra_search = keyword + "|" + user_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalBalanceBlock").html(json.total_wallet_balance);
      $("#totalActualBalanceBlock").html(json.total_actual_balance);
    }
  });
}

$("#releaseUpiBalanceReportSearchBtn").on("click", function () {
  $("#releaseUpiBalanceReportDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var user_type = $("#user_type").val();
  releaseUpiBalanceReportDataTable(keyword, user_type);
});

function commissionReportDataTable(keyword = "", user_type = "") {
  var siteUrl = $("#siteUrl").val();
  var commissionReportDataTable = $("#commissionReportDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getCommissionReport",
      data: function (d) {
        d.extra_search = keyword + "|" + user_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalBalanceBlock").html(json.total_wallet_balance);
      $("#totalActualBalanceBlock").html(json.total_actual_balance);
    }
  });
}

$("#commissionReportSearchBtn").on("click", function () {
  $("#commissionReportDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var user_type = $("#user_type").val();
  commissionReportDataTable(keyword, user_type);
});

function rechargeCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (status == 0) {
    var status = $("#status").val();
  }
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var rechargeCommisionDataTable = $("#rechargeCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getRechargeCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#rechargeCommisionSearchBtn").on("click", function () {
  $("#rechargeCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  rechargeCommisionDataTable(keyword, fromDate, toDate);
});

function bbpsCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (status == 0) {
    var status = $("#status").val();
  }
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var bbpsCommisionDataTable = $("#bbpsCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getBBPSCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#bbpsCommisionSearchBtn").on("click", function () {
  $("#bbpsCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  bbpsCommisionDataTable(keyword, fromDate, toDate);
});

function fundTransferCommisionDataTable(
  keyword = "",
  fromDate = "",
  toDate = ""
) {
  if (status == 0) {
    var status = $("#status").val();
  }
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var fundTransferCommisionDataTable = $(
    "#fundTransferCommisionDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getFundTransferCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#fundTransferCommisionSearchBtn").on("click", function () {
  $("#fundTransferCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  fundTransferCommisionDataTable(keyword, fromDate, toDate);
});

function moneyTransferCommisionDataTable(
  keyword = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var moneyTransferCommisionDataTable = $(
    "#moneyTransferCommisionDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getMoneyTransferCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#moneyTransferCommisionSearchBtn").on("click", function () {
  $("#moneyTransferCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  moneyTransferCommisionDataTable(keyword, fromDate, toDate);
});

function openPayoutCommisionDataTable(
  keyword = "",
  fromDate = "",
  toDate = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var openPayoutCommisionDataTable = $(
    "#openPayoutCommisionDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getOpenPayoutCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#openPayoutCommisionSearchBtn").on("click", function () {
  $("#openPayoutCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  openPayoutCommisionDataTable(keyword, fromDate, toDate);
});

function aepsCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var aepsCommisionDataTable = $("#aepsCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getAepsCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#aepsCommisionSearchBtn").on("click", function () {
  $("#aepsCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  aepsCommisionDataTable(keyword, fromDate, toDate);
});

function myAepsCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var myAepsCommisionDataTable = $("#myAepsCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getMyAepsCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#myAepsCommisionSearchBtn").on("click", function () {
  $("#myAepsCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  myAepsCommisionDataTable(keyword, fromDate, toDate);
});

function cashDepositeCommisionDataTable(
  keyword = "",
  fromDate = "",
  toDate = ""
) {
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var cashDepositeCommisionDataTable = $(
    "#cashDepositeCommisionDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getCashDepositeCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#cashDepositeCommisionSearchBtn").on("click", function () {
  $("#cashDepositeCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  cashDepositeCommisionDataTable(keyword, fromDate, toDate);
});

function upiCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var upiCommisionDataTable = $("#upiCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getUpiCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#upiCommisionSearchBtn").on("click", function () {
  $("#upiCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  upiCommisionDataTable(keyword, fromDate, toDate);
});

function referralCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var referralCommisionDataTable = $("#referralCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getReferralCommissionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#referralCommisionSearchBtn").on("click", function () {
  $("#referralCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  referralCommisionDataTable(keyword, fromDate, toDate);
});

function upiCashCommisionDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }
  var siteUrl = $("#siteUrl").val();
  var upiCashCommisionDataTable = $("#upiCashCommisionDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getUpiCashCommisionList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#upiCashCommisionSearchBtn").on("click", function () {
  $("#upiCashCommisionDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  upiCashCommisionDataTable(keyword, fromDate, toDate);
});

function moveMemberDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var moveMemberDataTable = $("#moveMemberDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[4, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/report/getMoveMemberList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#moveMemberSearchBtn").on("click", function () {
  $("#moveMemberDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  moveMemberDataTable(keyword);
});

function bbpsDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var bbpsDataTable = $("#bbpsDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[5, "desc"]],
    deferRender: true,
    ajax: {
      url: "getBBPSList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#bbpsSearchBtn").on("click", function () {
  $("#bbpsDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  bbpsDataTable(keyword, date);
});

function upiDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  user = "",
  type = 0,
  api_type = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (type == 0) {
    var type = $("#type").val();
  }

  if (api_type == 0) {
    var api_type = $("#api_type").val();
  }

  var upiDataTable = $("#upiDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiCollectionList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          user +
          "|" +
          type +
          "|" +
          api_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalSuccess);
      $("#totalSuccessChargeBlock").html(json.totalCharge);
      $("#totalChargebackRechargeBlock").html(json.totalChargeBack);
      $("#totalFailedRechargeBlock").html(json.totalFailed);
    }
  });
}

$("#upiSearchBtn").on("click", function () {
  $("#upiDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var from_date = $("#from_date").val();
  var to_date = $("#to_date").val();
  var user = $("#user").val();
  var type = $("#type").val();
  var api_type = $("#api_type").val();
  upiDataTable(keyword, from_date, to_date, user, type, api_type);
});

function upiChargeBackBtn(recordID) {
  $("#chargeBackBtn" + recordID).prop("disabled", true);
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/upiChargeBackAuth/" + recordID,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        Swal.fire({
          icon: "success",
          title: data["msg"]
        });
      }
      $("#upiDataTable").DataTable().destroy();
      var keyword = $("#keyword").val();
      var from_date = $("#from_date").val();
      var to_date = $("#to_date").val();
      upiDataTable(keyword, from_date, to_date);
    }
  });
}

function upiQrDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }

  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (user == "") {
    var user = $("#user").val();
  }
  var upiQrDataTable = $("#upiQrDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiQrList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
    }
  });
}

$("#upiQrSearchBtn").on("click", function () {
  $("#upiQrDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#fromDate").val();
  var toDate = $("#toDate").val();
  var user = $("#user").val();
  upiQrDataTable(keyword, fromDate, toDate, user);
});

function upiCheckStatusBtn(recordID) {
  $("#checkStatusBtn" + recordID).prop("disabled", true);
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/upiCheckStatusAuth/" + recordID,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      if (data["status"] == 1) {
        Swal.fire({
          icon: "success",
          title: data["msg"]
        });
      } else {
        Swal.fire({
          icon: "error",
          title: data["msg"]
        });
      }
      $("#upiQrDataTable").DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#fromDate").val();
      var toDate = $("#toDate").val();
      var user = $("#user").val();
      upiQrDataTable(keyword, fromDate, toDate, user);
    }
  });
}

function upiApiLogDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var date = $("#date").val();
  var upiApiLogDataTable = $("#upiApiLogDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiApiLogList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#upiApiLogSearchBtn").on("click", function () {
  $("#upiApiLogDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  upiApiLogDataTable(keyword, date);
});

function payoutApiLogDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var date = $("#date").val();
  var payoutApiLogDataTable = $("#payoutApiLogDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getPayoutApiLogList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#payoutApiLogSearchBtn").on("click", function () {
  $("#payoutApiLogDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  payoutApiLogDataTable(keyword, date);
});

function upiCashDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var date = $("#date").val();
  var upiCashDataTable = $("#upiCashDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiCashList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#upiCashSearchBtn").on("click", function () {
  $("#upiCashDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  upiCashDataTable(keyword, fromDate, toDate);
});

function utiPancardDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var date = $("#date").val();
  var utiPancardDataTable = $("#utiPancardDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUtiPancardList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#upiCashSearchBtn").on("click", function () {
  $("#utiPancardDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  utiPancardDataTable(keyword, fromDate, toDate);
});

function manualMoneyTransferDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();

  var manualMoneyTransferDataTable = $(
    "#manualMoneyTransferDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "transfer/getPaymentList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#manualTransferSearchBtn").on("click", function () {
  $("#manualMoneyTransferDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  manualMoneyTransferDataTable(keyword, date);
});

function adminBankTransferDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();

  var adminBankTransferDataTable = $("#adminBankTransferDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getPaymentList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#adminBankTransferSearchBtn").on("click", function () {
  $("#adminBankTransferDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  adminBankTransferDataTable(keyword, date);
});

function moneyTransferDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var moneyTransferDataTable = $("#moneyTransferDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getPaymentList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + status;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalSuccess);
      $("#totalPendingRechargeBlock").html(json.totalPending);
      $("#totalFailedRechargeBlock").html(json.totalFailed);
      $("#totalSuccessChargeBlock").html(json.totalCharge);
    }
  });
}

$("#moneyTransferSearchBtn").on("click", function () {
  $("#moneyTransferDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#selStatus").val();
  moneyTransferDataTable(keyword, fromDate, toDate, status);
});

function checkCibStatus(recordID) {
  var siteUrl = $("#siteUrl").val();
  $.ajax({
    url: siteUrl + "admin/report/checkCibStatus/" + recordID,
    success: function (r) {
      var data = JSON.parse($.trim(r));
      $("#moneyTransferDataTable").DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      var status = $("#selStatus").val();
      moneyTransferDataTable(keyword, fromDate, toDate, status);
    }
  });
}

function moneyTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  type = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (user == "") {
    var user = $("#user").val();
  }

  if (type == 0) {
    var type = $("#type").val();
  }

  var moneyTransferHistoryDataTable = $(
    "#moneyTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [{ targets: "_all", orderable: false }],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMoneyTransferList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
      $("#totalSuccessChargeBlock").html(
        json.successCharge + " / " + json.successRecord
      );
    },
    // Show loader when processing starts
    processing: function (e, settings, processing) {
      if (processing) {
        $("#loader").show();
      } else {
        $("#loader").hide();
      }
    }
  });
}

$("#moneyTransferHistorySearchBtn").on("click", function () {
  $("#moneyTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var type = $("#type").val();
  moneyTransferHistoryDataTable(keyword, fromDate, toDate, status, user, type);
});

function newMoneyTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  type = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (type == 0) {
    var type = $("#type").val();
  }

  var newMoneyTransferHistoryDataTable = $(
    "#newMoneyTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNewMoneyTransferList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
      $("#totalSuccessChargeBlock").html(
        json.successCharge + " / " + json.successRecord
      );
    }
  });
}

$("#newMoneyTransferHistorySearchBtn").on("click", function () {
  $("#newMoneyTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var type = $("#type").val();
  newMoneyTransferHistoryDataTable(
    keyword,
    fromDate,
    toDate,
    status,
    user,
    type
  );
});

function upiTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == "") {
    var status = $("#status").val();
  }
  if (user == "") {
    var user = $("#user").val();
  }

  var upiTransferHistoryDataTable = $("#upiTransferHistoryDataTable").DataTable(
    {
      dom: "Bfrtip",
      buttons: ["csv", "excel"],
      pageLength: 50,
      lengthMenu: [10, 25, 50, 75, 100],
      searching: false,
      columnDefs: [
        {
          targets: 0,
          orderable: false
        },
        {
          targets: 1,
          orderable: false
        },
        {
          targets: 2,
          orderable: false
        },
        {
          targets: 3,
          orderable: false
        },
        {
          targets: 4,
          orderable: false
        },
        {
          targets: 5,
          orderable: false
        },
        {
          targets: 6,
          orderable: false
        },
        {
          targets: 7,
          orderable: false
        },
        {
          targets: 8,
          orderable: false
        },
        {
          targets: 9,
          orderable: false
        },
        {
          targets: 10,
          orderable: false
        },
        {
          targets: 11,
          orderable: false
        }
      ],
      processing: true,
      serverSide: true,
      order: [[0, "desc"]],
      deferRender: true,
      ajax: {
        url: "getUpiTransferList",
        data: function (d) {
          d.extra_search =
            keyword + "|" + fromDate + "|" + toDate + "|" + status + "|" + user;
        }
      },
      initComplete: function (settings, json) {
        $("#totalSuccessIciciBlock").html(
          json.successAmount + " / " + json.successRecord
        );
        $("#totalPendingIciciBlock").html(
          json.pendingAmount + " / " + json.pendingRecord
        );
        $("#totalFailedIciciBlock").html(
          json.failedAmount + " / " + json.failedRecord
        );
        $("#totalSuccessChargeBlock").html(
          json.successCharge + " / " + json.successRecord
        );
      }
    }
  );
}

$("#upiTransferHistorySearchBtn").on("click", function () {
  $("#upiTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  upiTransferHistoryDataTable(keyword, fromDate, toDate, status, user);
});

function dmtHistoryDataTable(keyword = "", fromDate = "", toDate = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var dmtHistoryDataTable = $("#dmtHistoryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getDmtHistoryList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#dmtHistorySearchBtn").on("click", function () {
  $("#dmtHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  dmtHistoryDataTable(keyword, fromDate, toDate);
});

function accountDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var accountDataTable = $("#accountDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getAccountList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#accountSearchBtn").on("click", function () {
  $("#accountDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  accountDataTable(keyword, fromDate, toDate);
});

function iciciAccountDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var iciciAccountDataTable = $("#iciciAccountDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getIciciAccountList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#iciciAccountSearchBtn").on("click", function () {
  $("#iciciAccountDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  iciciAccountDataTable(keyword, fromDate, toDate);
});

function newAepsKycDataTable(keyword = "", date = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  var newAepsKycDataTable = $("#newAepsKycDataTable").DataTable({
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNewAepsKycList",
      data: function (d) {
        d.extra_search = keyword + "|" + date + "|" + user;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#newAepsKycSearchBtn").on("click", function () {
  $("#newAepsKycDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  var user = $("#user").val();
  newAepsKycDataTable(keyword, date, user);
});

function newAepsHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  service = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  if (service == "") {
    var service = $("#service").val();
  }

  var siteUrl = $("#siteUrl").val();
  var newAepsHistoryDataTable = $("#newAepsHistoryDataTable").DataTable({
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNewAepsHistoryList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          service;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessNewAepsBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
      $("#totalFailedNewAepsBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#newAepsHistorySearchBtn").on("click", function () {
  $("#newAepsHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var service = $("#service").val();
  newAepsHistoryDataTable(keyword, fromDate, toDate, status, user, service);
});

function dynamicInvoiceDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var dynamicInvoiceDataTable = $("#dynamicInvoiceDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["copy", "csv", "excel", "pdf"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[4, "desc"]],
    deferRender: true,
    ajax: {
      url: "getDynamicInvoiceList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#invoiceSearchBtn").on("click", function () {
  $("#dynamicInvoiceDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  dynamicInvoiceDataTable(keyword);
});

function invoiceSummeryDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var invoice_id = $("#invoice_id").val();

  var invoiceSummeryDataTable = $("#invoiceSummeryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[3, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/account/getDynamicInvoiceSummeryList",
      data: function (d) {
        d.extra_search = keyword + "|" + invoice_id;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#invoiceSummerySearchBtn").on("click", function () {
  $("#invoiceSummeryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  invoiceSummeryDataTable(keyword);
});

function nsdlPanCardListDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  user = ""
) {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var nsdlPanCardListDataTable = $("#nsdlPanCardListDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNsdlPanCardList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#nsdlPanCardListSearchBtn").on("click", function () {
  $("#nsdlPanCardListDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  nsdlPanCardListDataTable(keyword, fromDate, toDate, user);
});

function nsdlActivationDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var nsdlActivationDataTable = $("#nsdlActivationDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      },
      {
        targets: 13,
        orderable: false
      },
      {
        targets: 14,
        orderable: false
      },
      {
        targets: 15,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNsdlActivationList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#nsdlActivationSearchBtn").on("click", function () {
  $("#nsdlActivationDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  nsdlActivationDataTable(keyword, fromDate, toDate);
});

function gstDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (user == "") {
    var user = $("#user").val();
  }
  var gstDataTable = $("#gstDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getGstReportList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessGstBlock").html(json.successAmount);
    }
  });
}

$("#gstSearchBtn").on("click", function () {
  $("#gstDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  gstDataTable(keyword, fromDate, toDate, user);
});

function findPanDataTable(keyword = "", fromDate = "", toDate = "", user = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var findPanDataTable = $("#findPanDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getFindPanList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate + "|" + user;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#findPanSearchBtn").on("click", function () {
  $("#findPanDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var user = $("#user").val();
  findPanDataTable(keyword, fromDate, toDate, user);
});

function utiPanRequestDataTable(keyword = "", fromDate = "", toDate = "") {
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  var siteUrl = $("#siteUrl").val();
  var utiPanRequestDataTable = $("#utiPanRequestDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUtiBalanceList",
      data: function (d) {
        d.extra_search = keyword + "|" + fromDate + "|" + toDate;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#utiBalanceSearchBtn").on("click", function () {
  $("#utiPanRequestDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  utiPanRequestDataTable(keyword, fromDate, toDate);
});

function tdsInvoiceDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var tdsInvoiceDataTable = $("#tdsInvoiceDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["copy", "csv", "excel", "pdf"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[4, "desc"]],
    deferRender: true,
    ajax: {
      url: "getTdsInvoiceList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#tdsinvoiceSearchBtn").on("click", function () {
  $("#tdsInvoiceDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  tdsInvoiceDataTable(keyword);
});

function tdsInvoiceSummeryDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var invoice_id = $("#invoice_id").val();

  var tdsInvoiceSummeryDataTable = $("#tdsInvoiceSummeryDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[3, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl + "admin/account/getTdsInvoiceSummeryList",
      data: function (d) {
        d.extra_search = keyword + "|" + invoice_id;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#tdsInvoiceSummerySearchBtn").on("click", function () {
  $("#tdsInvoiceSummeryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  tdsInvoiceSummeryDataTable(keyword);
});

function manualInvoiceDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var manualInvoiceDataTable = $("#manualInvoiceDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["copy", "csv", "excel", "pdf"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[4, "desc"]],
    deferRender: true,
    ajax: {
      url: "getManualInvoiceList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}
$("#manualInvoiceSearchBtn").on("click", function () {
  $("#manualInvoiceDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  manualInvoiceDataTable(keyword);
});

function apiFundRequestDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var apiFundRequestDataTable = $("#apiFundRequestDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getApiFundRequestList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#searchApiRequestBtn").on("click", function () {
  $("#apiFundRequestDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  apiFundRequestDataTable(keyword, date);
});

function scanPayTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  var scanPayTransferHistoryDataTable = $(
    "#scanPayTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getScanPayTransferList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + fromDate + "|" + toDate + "|" + status + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
    }
  });
}

$("#scanPayTransferHistorySearchBtn").on("click", function () {
  $("#scanPayTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  scanPayTransferHistoryDataTable(keyword, fromDate, toDate, status, user);
});

function upiChargebackDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  user = "",
  api_type = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (api_type == 0) {
    var api_type = $("#api_type").val();
  }

  var upiChargebackDataTable = $("#upiChargebackDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      },
      {
        targets: 12,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getUpiChargebackList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + fromDate + "|" + toDate + "|" + user + "|" + api_type;
      }
    },
    initComplete: function (settings, json) {
      //$("#totalSuccessRechargeBlock").html(json.totalSuccess);
      //$("#totalSuccessChargeBlock").html(json.totalCharge);
      $("#totalFailedRechargeBlock").html(json.totalFailed);
    }
  });
}

$("#upiChargebackSearchBtn").on("click", function () {
  $("#upiChargebackDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var from_date = $("#from_date").val();
  var to_date = $("#to_date").val();
  var user = $("#user").val();
  var api_type = $("#api_type").val();
  upiChargebackDataTable(keyword, from_date, to_date, user, api_type);
});

// function openMoneyTransferHistoryDataTable(
//   keyword = "",
//   fromDate = "",
//   toDate = "",
//   status = 0,
//   user = "",
//   type = ""
// ) {
//   var siteUrl = $("#siteUrl").val();
//   if (fromDate == "") {
//     var fromDate = $("#from_date").val();
//   }
//   if (toDate == "") {
//     var toDate = $("#to_date").val();
//   }
//   if (status == 0) {
//     var status = $("#status").val();
//   }

//   var openMoneyTransferHistoryDataTable = $(
//     "#openMoneyTransferHistoryDataTable"
//   ).DataTable({
//     dom: "Bfrtip",
//     buttons: ["csv", "excel"],
//     processing: true,
//     pageLength: 50,
//     lengthMenu: [10, 25, 50, 75, 100],
//     searching: false,
//     columnDefs: [
//       {
//         targets: 0,
//         orderable: false
//       },
//       {
//         targets: 1,
//         orderable: false
//       },
//       {
//         targets: 2,
//         orderable: false
//       },
//       {
//         targets: 3,
//         orderable: false
//       },
//       {
//         targets: 4,
//         orderable: false
//       },
//       {
//         targets: 5,
//         orderable: false
//       },
//       {
//         targets: 6,
//         orderable: false
//       },
//       {
//         targets: 7,
//         orderable: false
//       },
//       {
//         targets: 8,
//         orderable: false
//       },
//       {
//         targets: 9,
//         orderable: false
//       },
//       {
//         targets: 10,
//         orderable: false
//       },
//       {
//         targets: 11,
//         orderable: false
//       },
//       {
//         targets: 12,
//         orderable: false
//       }
//     ],
//     processing: true,
//     serverSide: true,
//     order: [[0, "desc"]],
//     deferRender: true,
//     ajax: {
//       url: "getOpenMoneyTransferList",
//       data: function (d) {
//         d.extra_search =
//           keyword +
//           "|" +
//           fromDate +
//           "|" +
//           toDate +
//           "|" +
//           status +
//           "|" +
//           user +
//           "|" +
//           type;
//       }
//     },
//     initComplete: function (settings, json) {
//       $("#totalSuccessIciciBlock").html(
//         json.successAmount + " / " + json.successRecord
//       );
//       $("#totalPendingIciciBlock").html(
//         json.pendingAmount + " / " + json.pendingRecord
//       );
//       $("#totalFailedIciciBlock").html(
//         json.failedAmount + " / " + json.failedRecord
//       );
//       $("#totalSuccessChargeBlock").html(
//         json.successCharge + " / " + json.successRecord
//       );
//     }
//   });
// }

// $("#openMoneyTransferHistorySearchBtn").on("click", function () {
//   $("#openMoneyTransferHistoryDataTable").DataTable().destroy();
//   var keyword = $("#keyword").val();
//   var fromDate = $("#from_date").val();
//   var toDate = $("#to_date").val();
//   var status = $("#status").val();
//   var user = $("#user").val();
//   var type = $("#type").val();
//   openMoneyTransferHistoryDataTable(
//     keyword,
//     fromDate,
//     toDate,
//     status,
//     user,
//     type
//   );
// });

function openMoneyTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  type = ""
) {
  const siteUrl = $("#siteUrl").val();

  if (!fromDate) {
    fromDate = $("#from_date").val();
  }
  if (!toDate) {
    toDate = $("#to_date").val();
  }
  if (status === 0) {
    status = $("#status").val();
  }

  const openMoneyTransferHistoryDataTable = $(
    "#openMoneyTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    processing: true,
    columnDefs: Array.from({ length: 14 }, (_, i) => ({
      targets: i,
      orderable: false
    })),
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: siteUrl+"admin/report/getOpenMoneyTransferListCombined",
      data: function (d) {
        d.extra_search = [keyword, fromDate, toDate, status, user, type].join(
          "|"
        );
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        `${json.successAmount} / ${json.successRecord}`
      );
      $("#totalPendingIciciBlock").html(
        `${json.pendingAmount} / ${json.pendingRecord}`
      );
      $("#totalFailedIciciBlock").html(
        `${json.failedAmount} / ${json.failedRecord}`
      );
      $("#totalSuccessChargeBlock").html(
        `${json.successCharge} / ${json.successRecord}`
      );
    }
  });
}

$("#openMoneyTransferHistorySearchBtn").on("click", function () {
  const dataTable = $("#openMoneyTransferHistoryDataTable").DataTable();
  dataTable.destroy();

  const keyword = $("#keyword").val();
  const fromDate = $("#from_date").val();
  const toDate = $("#to_date").val();
  const status = $("#status").val();
  const user = $("#user").val();
  const type = $("#type").val();
  openMoneyTransferHistoryDataTable(
    keyword,
    fromDate,
    toDate,
    status,
    user,
    type
  );
});

function openPayoutSuccessBtn()
{
    var siteUrl = $("#siteUrl").val();
    var str = $("#openMoneySuccessForm").serialize();
      $.ajax({
        type: "POST",
        url: siteUrl + "admin/report/successOpenMoneyPayoutAjax",
        data: str,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          $("#updateComplainModel").modal("hide");
          alert(data['msg']);
          const dataTable = $("#openMoneyTransferHistoryDataTable").DataTable();
  dataTable.destroy();

  const keyword = $("#keyword").val();
  const fromDate = $("#from_date").val();
  const toDate = $("#to_date").val();
  const status = $("#status").val();
  const user = $("#user").val();
  const type = $("#type").val();
  openMoneyTransferHistoryDataTable(
    keyword,
    fromDate,
    toDate,
    status,
    user,
    type
  );
        }
      });
}

function refundOpenPayout(id)
{
    if(confirm("Are you sure you want to refund this transaction?")){
        var siteUrl = $("#siteUrl").val();
    
      $.ajax({
        type: "POST",
        url: siteUrl + "admin/report/refundOpenMoneyPayoutAjax/"+id,
        success: function (r) {
          var data = JSON.parse($.trim(r));
          alert(data['msg']);
          const dataTable = $("#openMoneyTransferHistoryDataTable").DataTable();
  dataTable.destroy();

  const keyword = $("#keyword").val();
  const fromDate = $("#from_date").val();
  const toDate = $("#to_date").val();
  const status = $("#status").val();
  const user = $("#user").val();
  const type = $("#type").val();
  openMoneyTransferHistoryDataTable(
    keyword,
    fromDate,
    toDate,
    status,
    user,
    type
  );
        }
      });
    }
    else{
        return false;
    }
}

function openMoneyUpiTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  var openMoneyUpiTransferHistoryDataTable = $(
    "#openMoneyUpiTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getOpenMoneyUpiTransferList",
      data: function (d) {
        d.extra_search =
          keyword + "|" + fromDate + "|" + toDate + "|" + status + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
      $("#totalSuccessChargeBlock").html(
        json.successCharge + " / " + json.successRecord
      );
    }
  });
}

$("#openMoneyUpiTransferHistorySearchBtn").on("click", function () {
  $("#openMoneyUpiTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  openMoneyUpiTransferHistoryDataTable(keyword, fromDate, toDate, status, user);
});

function settlementMoneyTransferHistoryDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = "",
  type = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  var settlementMoneyTransferHistoryDataTable = $(
    "#settlementMoneyTransferHistoryDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [{ targets: "_all", orderable: false }],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getSettlementTransferList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          status +
          "|" +
          user +
          "|" +
          type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
      $("#totalSuccessChargeBlock").html(
        json.successCharge + " / " + json.successRecord
      );
    }
  });
}

$("#settlementMoneyTransferHistorySearchBtn").on("click", function () {
  $("#settlementMoneyTransferHistoryDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  var type = $("#type").val();
  settlementMoneyTransferHistoryDataTable(
    keyword,
    fromDate,
    toDate,
    status,
    user,
    type
  );
});

function addFundDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  user = "",
  type = 0,
  api_type = 0
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }

  if (type == 0) {
    var type = $("#type").val();
  }

  if (api_type == 0) {
    var api_type = $("#api_type").val();
  }

  var addFundDataTable = $("#addFundDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getAddFundList",
      data: function (d) {
        d.extra_search =
          keyword +
          "|" +
          fromDate +
          "|" +
          toDate +
          "|" +
          user +
          "|" +
          type +
          "|" +
          api_type;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessRechargeBlock").html(json.totalSuccess);
      $("#totalSuccessChargeBlock").html(json.totalCharge);
      $("#totalChargebackRechargeBlock").html(json.totalChargeBack);
      $("#totalFailedRechargeBlock").html(json.totalFailed);
    }
  });
}

$("#addFundSearchBtn").on("click", function () {
  $("#addFundDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var from_date = $("#from_date").val();
  var to_date = $("#to_date").val();
  var user = $("#user").val();
  var type = $("#type").val();
  var api_type = $("#api_type").val();
  addFundDataTable(keyword, from_date, to_date, user, type, api_type);
});

function newMoneyTransferHistoryOldDataTable(
  keyword = "",
  fromDate = "",
  toDate = "",
  status = 0,
  user = ""
) {
  var siteUrl = $("#siteUrl").val();
  if (fromDate == "") {
    var fromDate = $("#from_date").val();
  }
  if (toDate == "") {
    var toDate = $("#to_date").val();
  }
  if (status == 0) {
    var status = $("#status").val();
  }

  var newMoneyTransferHistoryOldDataTable = $(
    "#newMoneyTransferHistoryOldDataTable"
  ).DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      },
      {
        targets: 10,
        orderable: false
      },
      {
        targets: 11,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getNewMoneyTransferListOld",
      data: function (d) {
        d.extra_search =
          keyword + "|" + fromDate + "|" + toDate + "|" + status + "|" + user;
      }
    },
    initComplete: function (settings, json) {
      $("#totalSuccessIciciBlock").html(
        json.successAmount + " / " + json.successRecord
      );
      $("#totalPendingIciciBlock").html(
        json.pendingAmount + " / " + json.pendingRecord
      );
      $("#totalFailedIciciBlock").html(
        json.failedAmount + " / " + json.failedRecord
      );
      $("#totalSuccessChargeBlock").html(
        json.successCharge + " / " + json.successRecord
      );
    }
  });
}

$("#newMoneyTransferHistoryOldSearchBtn").on("click", function () {
  $("#newMoneyTransferHistoryOldDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var fromDate = $("#from_date").val();
  var toDate = $("#to_date").val();
  var status = $("#status").val();
  var user = $("#user").val();
  newMoneyTransferHistoryOldDataTable(keyword, fromDate, toDate, status, user);
});

function memberRequestDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var memberRequestDataTable = $("#memberRequestDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getMemberRequestList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {
      //$("#totalSuccessRechargeBlock").html(json.totalBalance);
    }
  });
}

$("#memberSearchBtn").on("click", function () {
  $("#memberRequestDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  memberRequestDataTable(keyword, date);
});

function kycDataTable(keyword = "") {
  var siteUrl = $("#siteUrl").val();
  var kycDataTable = $("#kycDataTable").DataTable({
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      },
      {
        targets: 5,
        orderable: false
      },
      {
        targets: 6,
        orderable: false
      },
      {
        targets: 7,
        orderable: false
      },
      {
        targets: 8,
        orderable: false
      },
      {
        targets: 9,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[6, "desc"]],
    deferRender: true,
    ajax: {
      url: "getkycList",
      data: function (d) {
        d.extra_search = keyword;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#kycSearchBtn").on("click", function () {
  $("#kycDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();

  kycDataTable(keyword);
});

function aepsApiLogDataTable(keyword = "", date = "") {
  var siteUrl = $("#siteUrl").val();
  var date = $("#date").val();
  var aepsApiLogDataTable = $("#aepsApiLogDataTable").DataTable({
    dom: "Bfrtip",
    buttons: ["csv", "excel"],
    pageLength: 50,
    lengthMenu: [10, 25, 50, 75, 100],
    searching: false,
    columnDefs: [
      {
        targets: 0,
        orderable: false
      },
      {
        targets: 1,
        orderable: false
      },
      {
        targets: 2,
        orderable: false
      },
      {
        targets: 3,
        orderable: false
      },
      {
        targets: 4,
        orderable: false
      }
    ],
    processing: true,
    serverSide: true,
    order: [[0, "desc"]],
    deferRender: true,
    ajax: {
      url: "getAepsApiLogList",
      data: function (d) {
        d.extra_search = keyword + "|" + date;
      }
    },
    initComplete: function (settings, json) {}
  });
}

$("#aepsApiLogSearchBtn").on("click", function () {
  $("#aepsApiLogDataTable").DataTable().destroy();
  var keyword = $("#keyword").val();
  var date = $("#date").val();
  aepsApiLogDataTable(keyword, date);
});
