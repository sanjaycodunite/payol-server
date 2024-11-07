
			$('#example').DataTable();
      $('#example1').DataTable();
      dashboardApiDataTable();
      walletDataTable();
      eWalletDataTable();
      cWalletDataTable();
      vanWalletDataTable();
      creditDataTable();
      debitDataTable();
      virtualCreditDataTable();
      virtualDebitDataTable();
      fundRequestDataTable();

      rechargeDataTable();
      bbpsDataTable();
      moneyTransferDataTable();
      rechargeCommisionDataTable();
      bbpsCommisionDataTable();
      fundTransferCommisionDataTable();
      balanceReportDataTable();

      apiDataTable();
      bbpsWalletDataTable();
      bbpsHistoryDataTable();
      upiDataTable();
      currentAccountDataTable();
      axisAccountDataTable();
      cashDepositeDataTable();
      comWalletDataTable();
      moneyTransferHistoryDataTable();
      aepsKycDataTable();
      aepsHistoryDataTable();
      matmHistoryDataTable();
      upiCashDataTable();
      accountDataTable();
      moneyTransferCommisionDataTable();
      openPayoutCommisionDataTable();
      aepsCommisionDataTable();
      cashDepositeCommisionDataTable();
      upiCommisionDataTable();
      upiCashCommisionDataTable();
      gatewayHistoryDataTable();
      dmtHistoryDataTable();
      virtualHistoryDataTable();
      utiPancardDataTable();
      moveMemberDataTable();

      dmtKycExportDataTable();
      dmtKycImportDataTable();
      dmtKycImportFileDataTable();
      nsdlListDataTable();

      function gatewayHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var gatewayHistoryDataTable = $('#gatewayHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTopupHistory",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#gatewayHistorySearchBtn').on('click', function() {
      $('#gatewayHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      gatewayHistoryDataTable(keyword,fromDate,toDate);
     });

     function nsdlListDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var nsdlListDataTable = $('#nsdlListDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false},{"targets": 12,"orderable": false},{"targets": 13,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getNsdlList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#nsdlListSearchBtn').on('click', function() {
        $('#nsdlListDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        nsdlListDataTable(keyword,fromDate,toDate);
      }); 

     function moveMemberDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var moveMemberDataTable = $('#moveMemberDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 4, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getMoveMemberList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#moveMemberSearchBtn').on('click', function() {
      $('#moveMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        moveMemberDataTable(keyword,fromDate,toDate);
     });

     function utiPancardDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var date = $("#date").val();
        var utiPancardDataTable = $('#utiPancardDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUtiPancardList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#upiCashSearchBtn').on('click', function() {
      $('#utiPancardDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      utiPancardDataTable(keyword,fromDate,toDate);
     });


     function virtualHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var virtualHistoryDataTable = $('#virtualHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getVirtualHistory",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#virtualHistorySearchBtn').on('click', function() {
      $('#virtualHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      virtualHistoryDataTable(keyword,fromDate,toDate);
     });

      function upiCashCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var upiCashCommisionDataTable = $('#upiCashCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getUpiCashCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#upiCashCommisionSearchBtn').on('click', function() {
      $('#upiCashCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        upiCashCommisionDataTable(keyword,fromDate,toDate);
     });


      function upiCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var upiCommisionDataTable = $('#upiCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getUpiCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#upiCommisionSearchBtn').on('click', function() {
      $('#upiCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        upiCommisionDataTable(keyword,fromDate,toDate);
     });

      function cashDepositeCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var cashDepositeCommisionDataTable = $('#cashDepositeCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getCashDepositeCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#cashDepositeCommisionSearchBtn').on('click', function() {
      $('#cashDepositeCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        cashDepositeCommisionDataTable(keyword,fromDate,toDate);
     });

      function aepsCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var aepsCommisionDataTable = $('#aepsCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getAepsCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#aepsCommisionSearchBtn').on('click', function() {
      $('#aepsCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        aepsCommisionDataTable(keyword,fromDate,toDate);
     });

      function moneyTransferCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var moneyTransferCommisionDataTable = $('#moneyTransferCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getMoneyTransferCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#moneyTransferCommisionSearchBtn').on('click', function() {
      $('#moneyTransferCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        moneyTransferCommisionDataTable(keyword,fromDate,toDate);
     });

     function openPayoutCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var openPayoutCommisionDataTable = $('#openPayoutCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getOpenPayoutCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#openPayoutCommisionSearchBtn').on('click', function() {
      $('#openPayoutCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        openPayoutCommisionDataTable(keyword,fromDate,toDate);
     });


      function rechargeCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var rechargeCommisionDataTable = $('#rechargeCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getRechargeCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#rechargeCommisionSearchBtn').on('click', function() {
      $('#rechargeCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        rechargeCommisionDataTable(keyword,fromDate,toDate);
     });

     function bbpsCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var bbpsCommisionDataTable = $('#bbpsCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getBBPSCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#bbpsCommisionSearchBtn').on('click', function() {
      $('#bbpsCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        bbpsCommisionDataTable(keyword,fromDate,toDate);
     });


      function accountDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var accountDataTable = $('#accountDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAccountList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#accountSearchBtn').on('click', function() {
      $('#accountDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      accountDataTable(keyword,fromDate,toDate);
     });

      function upiCashDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var date = $("#date").val();
        var upiCashDataTable = $('#upiCashDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiCashList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#upiCashSearchBtn').on('click', function() {
      $('#upiCashDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      upiCashDataTable(keyword,fromDate,toDate);
     });

      function aepsHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var aepsHistoryDataTable = $('#aepsHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAepsHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#aepsHistorySearchBtn').on('click', function() {
      $('#aepsHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      aepsHistoryDataTable(keyword,fromDate,toDate);
     });

     function matmHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var matmHistoryDataTable = $('#matmHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMatmHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#matmHistorySearchBtn').on('click', function() {
      $('#matmHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      matmHistoryDataTable(keyword,fromDate,toDate);
     });



      function aepsKycDataTable(keyword = '',fromDate='', toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var aepsKycDataTable = $('#aepsKycDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAepsKycList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#aepsKycSearchBtn').on('click', function() {
      $('#aepsKycDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      aepsKycDataTable(keyword,fromDate,toDate);
     });

      function moneyTransferHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var moneyTransferHistoryDataTable = $('#moneyTransferHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMoneyTransferList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#moneyTransferHistorySearchBtn').on('click', function() {
        $('#moneyTransferHistoryDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        moneyTransferHistoryDataTable(keyword,fromDate,toDate);
      });


       function dmtHistoryDataTable(keyword = '',fromDate='', toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var dmtHistoryDataTable = $('#dmtHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDmtHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#dmtHistorySearchBtn').on('click', function() {
        $('#dmtHistoryDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        dmtHistoryDataTable(keyword,fromDate,toDate);
      });



      function upiDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var date = $("#date").val();
        var upiDataTable = $('#upiDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiCollectionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#upiSearchBtn').on('click', function() {
      $('#upiDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      upiDataTable(keyword,fromDate,toDate);
     });

     function cashDepositeDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var cashDepositeDataTable = $('#cashDepositeDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getCashDepositeList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#cashDepositeSearchBtn').on('click', function() {
        $('#cashDepositeDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        cashDepositeDataTable(keyword,fromDate,toDate);
      });

     function currentAccountDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var currentAccountDataTable = $('#currentAccountDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getCurrentAccountList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#currentAccountSearchBtn').on('click', function() {
        $('#currentAccountDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        currentAccountDataTable(keyword,fromDate,toDate);
      });

      function axisAccountDataTable(keyword = '',fromDate='', toDate = '')
      { 
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var axisAccountDataTable = $('#axisAccountDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getAxisAccountList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#axisAccountSearchBtn').on('click', function() {
        $('#axisAccountDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        axisAccountDataTable(keyword,fromDate,toDate);
      });

      function dashboardApiDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var dashboardApiDataTable = $('#dashboardApiDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "bPaginate": false,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          deferRender: true,
          "ajax": {
            "url": "dashboard/getAPIBalanceData",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
           

          }
            
          
        });

        /*setInterval( function () {
            dashboardApiDataTable.ajax.reload();
        }, 60000 );*/
      }

      function apiDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var apiDataTable = $('#apiDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAPIList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 
      
      
      function walletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var walletDataTable = $('#walletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


      $('#walletSearchBtn').on('click', function() {
        $('#walletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        walletDataTable(keyword,member_id,date);
      }); 

      function bbpsWalletDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var bbpsWalletDataTable = $('#bbpsWalletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getBBPSWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


      $('#bbpsWalletSearchBtn').on('click', function() {
        $('#bbpsWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        bbpsWalletDataTable(keyword,date);
      }); 

      function eWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var eWalletDataTable = $('#eWalletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

      
      $('#eWalletSearchBtn').on('click', function() {
        $('#eWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        eWalletDataTable(keyword,member_id,date);
      }); 

      function cWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var cWalletDataTable = $('#cWalletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

      
      $('#cWalletSearchBtn').on('click', function() {
        $('#cWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        cWalletDataTable(keyword,member_id,date);
      }); 

      function vanWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var vanWalletDataTable = $('#vanWalletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

      
      $('#vanWalletSearchBtn').on('click', function() {
        $('#vanWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        vanWalletDataTable(keyword,member_id,date);
      }); 

      function comWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var comWalletDataTable = $('#comWalletDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

      
      $('#comWalletSearchBtn').on('click', function() {
        $('#comWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        comWalletDataTable(keyword,member_id,date);
      }); 
      
      function creditDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var creditDataTable = $('#creditDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getCreditList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      } 


      

      $('#creditSearchBtn').on('click', function() {
        $('#creditDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();  
        var date = $("#date").val();
        creditDataTable(keyword,member_id,date);
      });

      function virtualCreditDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var virtualCreditDataTable = $('#virtualCreditDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getCreditList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      } 


      

      $('#virtualCreditSearchBtn').on('click', function() {
        $('#virtualCreditDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();  
        var date = $("#date").val();
        virtualCreditDataTable(keyword,member_id,date);
      });

      function debitDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var debitDataTable = $('#debitDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDebitList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      } 

      $('#debitSearchBtn').on('click', function() {
        $('#debitDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();  
        debitDataTable(keyword,member_id,date);
      });

      function virtualDebitDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var virtualDebitDataTable = $('#virtualDebitDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDebitList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      } 

      $('#virtualDebitSearchBtn').on('click', function() {
        $('#virtualDebitDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();  
        virtualDebitDataTable(keyword,member_id,date);
      });

      function fundRequestDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var fundRequestDataTable = $('#fundRequestDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getRequestList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            

          }
            
          
        });
      

      
      }


     $('#searchRequestBtn').on('click', function() {
      $('#fundRequestDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      fundRequestDataTable(keyword,date);
     });


     function rechargeDataTable(status = 0,keyword = '',fromDate = '',toDate = '',user_type = '',operator = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var rechargeDataTable = $('#rechargeDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getRechargeList",
              "data": function ( d ) {

              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status+'|'+user_type+'|'+operator;
              
            }



          },
          "initComplete": function( settings, json ) {
            
            $("#totalSuccessRechargeBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedRechargeBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });


       /* setInterval( function () {
            rechargeDataTable.ajax.reload();
        }, 60000 );*/
      

      
      }


     $('#rechargeSearchBtn').on('click', function() {
      $('#rechargeDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        var user_type = $("#user_type").val();
        var operator = $("#operator").val();
        rechargeDataTable(status,keyword,fromDate,toDate,user_type,operator);
     });

     function bbpsHistoryDataTable(status = 0,keyword = '',fromDate = '',toDate = '',user_type = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var bbpsHistoryDataTable = $('#bbpsHistoryDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getBbpsHistoryList",
              "data": function ( d ) {

              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status+'|'+user_type;
              
            }



          },
          "initComplete": function( settings, json ) {
            
            $("#totalSuccessBBPSBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingBBPSBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedBBPSBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });


       /* setInterval( function () {
            rechargeDataTable.ajax.reload();
        }, 60000 );*/
      

      
      }


     $('#bbpsHistorySearchBtn').on('click', function() {
      $('#bbpsHistoryDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        var user_type = $("#user_type").val();
        
        bbpsHistoryDataTable(status,keyword,fromDate,toDate,user_type);
     });

     function bbpsDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var bbpsDataTable = $('#bbpsDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getBBPSList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#bbpsSearchBtn').on('click', function() {
      $('#bbpsDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      bbpsDataTable(keyword,date);
     });


      function moneyTransferDataTable(keyword = '',fromDate = '',toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var moneyTransferDataTable = $('#moneyTransferDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#moneyTransferSearchBtn').on('click', function() {
        $('#moneyTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        moneyTransferDataTable(keyword,fromDate,toDate);
      });

      function dmtKycExportDataTable(member_id = '')
      { 

        var siteUrl = $("#siteUrl").val();
        var dmtKycExportDataTable = $('#dmtKycExportDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDmtKycExportList",
              "data": function ( d ) {
              d.extra_search = member_id;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#dmtKycExportSearchBtn').on('click', function() {
        $('#dmtKycExportDataTable').DataTable().destroy();
        var keyword = $("#member_id").val();
        dmtKycExportDataTable(keyword);
      });

      function dmtKycImportDataTable(member_id = '')
      { 

        var siteUrl = $("#siteUrl").val();
        var dmtKycImportDataTable = $('#dmtKycImportDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDmtKycImportList",
              "data": function ( d ) {
              d.extra_search = member_id;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

      function dmtKycImportFileDataTable(member_id = '')
      { 

        var siteUrl = $("#siteUrl").val();
        var file_id = $("#file_id").val();
        var dmtKycImportFileDataTable = $('#dmtKycImportFileDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getDmtKycImportFileDataList",
              "data": function ( d ) {
              d.extra_search = file_id;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
       


     function fundTransferCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '')
        {
          var fromDate = $("#from_date").val();
        }
        if(toDate == '')
        {
          var toDate = $("#to_date").val();
        }
        var siteUrl = $("#siteUrl").val();
        var fundTransferCommisionDataTable = $('#fundTransferCommisionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getFundTransferCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#fundTransferCommisionSearchBtn').on('click', function() {
      $('#fundTransferCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        fundTransferCommisionDataTable(keyword,fromDate,toDate);
     });

     
     function balanceReportDataTable(keyword = '',user_type = '')
      { 
        
        var siteUrl = $("#siteUrl").val();
        var balanceReportDataTable = $('#balanceReportDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superadmin/report/getBalanceReport",
              "data": function ( d ) {

              d.extra_search = keyword+'|'+user_type;
              
            }



          },
          "initComplete": function( settings, json ) {
            
            $("#totalBalanceBlock").html(json.total_wallet_balance);
            
          }
            
          
        });

      }


     $('#balanceReportSearchBtn').on('click', function() {
      $('#balanceReportDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var user_type = $("#user_type").val();
        balanceReportDataTable(keyword,user_type);
     });

			
			
		
