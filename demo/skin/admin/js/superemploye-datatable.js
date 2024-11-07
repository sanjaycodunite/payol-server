
			$('#example').DataTable();
      $('#example1').DataTable();
      dashboardApiDataTable();
      walletDataTable();
      eWalletDataTable();
      cWalletDataTable();
      creditDataTable();
      debitDataTable();
      fundRequestDataTable();

      rechargeDataTable();
      bbpsDataTable();
      moneyTransferDataTable();
      rechargeCommisionDataTable();
      fundTransferCommisionDataTable();
      balanceReportDataTable();

      apiDataTable();
      bbpsWalletDataTable();
      bbpsHistoryDataTable();
      upiDataTable();
      currentAccountDataTable();
      cashDepositeDataTable();
      comWalletDataTable();
      moneyTransferHistoryDataTable();
      aepsKycDataTable();
      aepsHistoryDataTable();
      upiCashDataTable();
      accountDataTable();
      moneyTransferCommisionDataTable();
      aepsCommisionDataTable();
      cashDepositeCommisionDataTable();
      upiCommisionDataTable();
      upiCashCommisionDataTable();
      gatewayHistoryDataTable();

      function gatewayHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var gatewayHistoryDataTable = $('#gatewayHistoryDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTopupHistory",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#gatewayHistorySearchBtn').on('click', function() {
      $('#gatewayHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      gatewayHistoryDataTable(keyword,date);
     });

      function upiCashCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var upiCashCommisionDataTable = $('#upiCashCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getUpiCashCommisionList",
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
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var upiCommisionDataTable = $('#upiCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getUpiCommisionList",
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
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var cashDepositeCommisionDataTable = $('#cashDepositeCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getCashDepositeCommisionList",
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
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var aepsCommisionDataTable = $('#aepsCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getAepsCommisionList",
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
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var moneyTransferCommisionDataTable = $('#moneyTransferCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getMoneyTransferCommisionList",
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


      function rechargeCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var rechargeCommisionDataTable = $('#rechargeCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getRechargeCommisionList",
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


      function accountDataTable(keyword = '')
      { 

        var siteUrl = $("#siteUrl").val();
        var accountDataTable = $('#accountDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAccountList",
              "data": function ( d ) {
              d.extra_search = keyword;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#accountSearchBtn').on('click', function() {
      $('#accountDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      
      accountDataTable(keyword);
     });

      function upiCashDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var date = $("#date").val();
        var upiCashDataTable = $('#upiCashDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiCashList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#upiCashSearchBtn').on('click', function() {
      $('#upiCashDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      upiCashDataTable(keyword,date);
     });

      function aepsHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var aepsHistoryDataTable = $('#aepsHistoryDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAepsHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#aepsHistorySearchBtn').on('click', function() {
      $('#aepsHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      aepsHistoryDataTable(keyword,date);
     });



      function aepsKycDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var aepsKycDataTable = $('#aepsKycDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getAepsKycList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#aepsKycSearchBtn').on('click', function() {
      $('#aepsKycDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      aepsKycDataTable(keyword,date);
     });

      function moneyTransferHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var moneyTransferHistoryDataTable = $('#moneyTransferHistoryDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMoneyTransferList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#moneyTransferHistorySearchBtn').on('click', function() {
        $('#moneyTransferHistoryDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        moneyTransferHistoryDataTable(keyword,date);
      });



      function upiDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var date = $("#date").val();
        var upiDataTable = $('#upiDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiCollectionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#upiSearchBtn').on('click', function() {
      $('#upiDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      upiDataTable(keyword,date);
     });

     function cashDepositeDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var cashDepositeDataTable = $('#cashDepositeDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getCashDepositeList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#cashDepositeSearchBtn').on('click', function() {
        $('#cashDepositeDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        cashDepositeDataTable(keyword,date);
      });

     function currentAccountDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var currentAccountDataTable = $('#currentAccountDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getCurrentAccountList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#currentAccountSearchBtn').on('click', function() {
        $('#currentAccountDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        currentAccountDataTable(keyword,date);
      });

      function dashboardApiDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var dashboardApiDataTable = $('#dashboardApiDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "bPaginate": false,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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

      function comWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var comWalletDataTable = $('#comWalletDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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

      function debitDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var debitDataTable = $('#debitDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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

      function fundRequestDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var fundRequestDataTable = $('#fundRequestDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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
        var siteUrl = $("#siteUrl").val();
        var rechargeDataTable = $('#rechargeDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getRechargeList",
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
        var siteUrl = $("#siteUrl").val();
        var bbpsHistoryDataTable = $('#bbpsHistoryDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getBbpsHistoryList",
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
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


      function moneyTransferDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var moneyTransferDataTable = $('#moneyTransferDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#moneyTransferSearchBtn').on('click', function() {
        $('#moneyTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        moneyTransferDataTable(keyword,date);
      });


       


     function fundTransferCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var fundTransferCommisionDataTable = $('#fundTransferCommisionDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getFundTransferCommisionList",
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
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"superemploye/report/getBalanceReport",
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

			
			
		
