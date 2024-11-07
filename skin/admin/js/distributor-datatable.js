$(document).ready(function() {
			$('#example').DataTable();
      employeDataTable();
      closeClubDataTable();
      mdMemberDataTable();
      distributorMemberDataTable();
      retailerMemberDataTable();
      userMemberDataTable();
      apiMemberDataTable();
      walletDataTable();
      creditDataTable();
      debitDataTable();
      fundRequestDataTable();
      myWalletDataTable();
      myFundRequestDataTable();
      rechargeDataTable();
      bbpsDataTable();
      manualMoneyTransferDataTable();
      payoutOpenDataTable();
      moneyTransferDataTable();
      newMoneyTransferDataTable();
      upiTransferDataTable();
      moneyTransferHistoryDataTable();
      ticketDataTable();
      rechargeCommisionDataTable();
      fundTransferCommisionDataTable();
      moneyTransferCommisionDataTable();
      aepsCommisionDataTable();
      cashDepositeCommisionDataTable();
      upiCommisionDataTable();
      upiCashCommisionDataTable();
      complainDataTable();
      bbpsHistoryDataTable();
      topupHistoryDataTable();
      aepsTxnDataTable();
      iciciAepsTxnDataTable();
      upiTxnDataTable();
      upiCashTxnDataTable();
      currentAccountDataTable();
      cashDepositeDataTable();
      dmtDataTable();
      matmHistoryDataTable();
      nsdlListDataTable();
      axisAccountDataTable();
      NewAepsTxnDataTable();
      nsdlPanDataTable();
      nsdlPanCardDataTable();
      utiPanRequestDataTable();
      settlementTransferDataTable();
      settlementMoneyTransferDataTable();
      upiQrDataTable();


      function NewAepsTxnDataTable(keyword = '', fromDate='', toDate = '' , status = 0, service = '')
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

         if(service == '')
        {
          var service = $("#service").val();
        }

        var siteUrl = $("#siteUrl").val();
        var NewAepsTxnDataTable = $('#NewAepsTxnDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTransactionList",
              "data": function ( d ) {
               d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status+'|'+service;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessNewAepsBlock").html(json.successAmount+' / '+json.successRecord);
            //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedNewAepsBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      } 


         $('#newAepsHistorySearchBtn').on('click', function() {
      $('#NewAepsTxnDataTable').DataTable().destroy();
          var keyword = $("#keyword").val();
           var fromDate = $("#from_date").val();
          var toDate = $("#to_date").val();
           var status = $("#status").val();        
            var service = $("#service").val();
          NewAepsTxnDataTable(keyword,fromDate,toDate,status,service);
     });

      
      
      function dmtDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var dmtDataTable = $('#dmtDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "dmt/getPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#dmtSearchBtn').on('click', function() {
        $('#dmtDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        dmtDataTable(keyword,date);
      });

       function axisAccountDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var axisAccountDataTable = $('#axisAccountDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/report/getAxisAccountList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       function matmHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var matmHistoryDataTable = $('#matmHistoryDataTable').DataTable({
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
            "url": "getMatmHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#matmHistorySearchBtn').on('click', function() {
      $('#matmHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      matmHistoryDataTable(keyword,date);
     });

     function nsdlListDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var nsdlListDataTable = $('#nsdlListDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getNsdlList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#nsdlListSearchBtn').on('click', function() {
        $('#nsdlListDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        nsdlListDataTable(keyword,date);
      });
      function employeDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var employeDataTable = $('#employeDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMemberList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#employeSearchBtn').on('click', function() {
        $('#employeDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        employeDataTable(keyword,date);
      });

       function closeClubDataTable()
      { 

        var siteUrl = $("#siteUrl").val();
        var closeClubDataTable = $('#closeClubDataTable').DataTable({
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
            "url": "getCloseClubList",
              "data": function ( d ) {
              
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

        function upiCashTxnDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var upiCashTxnDataTable = $('#upiCashTxnDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiCashTxnList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#upiCashTxnSearchBtn').on('click', function() {
        $('#upiCashTxnDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        upiCashTxnDataTable(keyword,date);
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
            "url": siteUrl+"distributor/report/getUpiCashCommisionList",
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
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
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

       function upiTxnDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var upiTxnDataTable = $('#upiTxnDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiTxnList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#upiTxnSearchBtn').on('click', function() {
        $('#upiTxnDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        upiTxnDataTable(keyword,date);
      });

      function mdMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var mdMemberDataTable = $('#mdMemberDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMDMemberList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#mdMemberSearchBtn').on('click', function() {
        $('#mdMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        mdMemberDataTable(keyword,date);
      });

       function distributorMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var distributorMemberDataTable = $('#distributorMemberDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getDistributorList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#distributorMemberSearchBtn').on('click', function() {
        $('#distributorMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        distributorMemberDataTable(keyword,date);
      });

      function retailerMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var retailerMemberDataTable = $('#retailerMemberDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getRetailerList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#retailerMemberSearchBtn').on('click', function() {
        $('#retailerMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        retailerMemberDataTable(keyword,date);
      });

      function userMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var userMemberDataTable = $('#userMemberDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUserList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#userMemberSearchBtn').on('click', function() {
        $('#userMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        userMemberDataTable(keyword,date);
      });

      function apiMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var apiMemberDataTable = $('#apiMemberDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getApiMemberList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 

       $('#apiMemberSearchBtn').on('click', function() {
        $('#apiMemberDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date = $("#date").val();
        apiMemberDataTable(keyword,date);
      });

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
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getwalletList",
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

      function ticketDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var ticketDataTable = $('#ticketDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTicketList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#ticketSearchBtn').on('click', function() {
        $('#ticketDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        
        ticketDataTable(keyword);
      }); 

      function myWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var myWalletDataTable = $('#myWalletDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMyWalletList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#myWalletSearchBtn').on('click', function() {
        $('#myWalletDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
        var date = $("#date").val();
        myWalletDataTable(keyword,member_id,date);
      }); 

      function topupHistoryDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var topupHistoryDataTable = $('#topupHistoryDataTable').DataTable({
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
            "url": "getTopupHistory",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

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
            "url": "getcreditList",
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
            "url": "getdebitList",
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


     function myFundRequestDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var myFundRequestDataTable = $('#myFundRequestDataTable').DataTable({
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
            "url": "getMyRequestList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#searchMyRequestBtn').on('click', function() {
      $('#myFundRequestDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      myFundRequestDataTable(keyword,date);
     });

     function rechargeDataTable(keyword = '',fromDate = '',toDate = '',status = 0)
      { 
         if(status == 0)
        {
          var status = $("#status").val();
        }
        if(fromDate == '' && toDate == '')
        {
          var fromDate = $("#from_date").val();
          var toDate = $("#to_date").val();
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
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getRechargeList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessRechargeBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedRechargeBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }


     $('#rechargeSearchBtn').on('click', function() {
      $('#rechargeDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
      var status = $("#status").val();
      rechargeDataTable(keyword,fromDate,toDate,status);
     });

     function complainDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var complainDataTable = $('#complainDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/complain/getComplainList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+member_id+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }
      $('#complainSearchBtn').on('click', function() {
        $('#complainDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        
        complainDataTable(keyword);
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
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/current/getAccountList",
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

      function bbpsHistoryDataTable(keyword = '',fromDate = '',toDate = '',status = 0)
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
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false},{"targets": 12,"orderable": false},{"targets": 13,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/report/getBBPSHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
             $("#totalSuccessBBPSBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingBBPSBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedBBPSBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }


     $('#bbpsHistorySearchBtn').on('click', function() {
      $('#bbpsHistoryDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        bbpsHistoryDataTable(keyword,fromDate,toDate,status);
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
            "url": siteUrl+"distributor/report/getRechargeCommisionList",
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
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/report/getFundTransferCommisionList",
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
            "url": siteUrl+"distributor/report/getMoneyTransferCommisionList",
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
            "url": siteUrl+"distributor/report/getAepsCommisionList",
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
            "url": siteUrl+"distributor/report/getCashDepositeCommisionList",
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
            "url": siteUrl+"distributor/report/getUpiCommisionList",
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
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
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

     function manualMoneyTransferDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var manualMoneyTransferDataTable = $('#manualMoneyTransferDataTable').DataTable({
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
            "url": "transfer/getPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#manualTransferSearchBtn').on('click', function() {
        $('#manualMoneyTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        manualMoneyTransferDataTable(keyword,date);
      });



        function newMoneyTransferDataTable(keyword = '',fromDate='', toDate = '' ,status = 0)
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
        var newMoneyTransferDataTable = $('#newMoneyTransferDataTable').DataTable({
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
            "url": "getNewPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessIciciBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingIciciBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedIciciBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }

      $('#newTransferSearchBtn').on('click', function() {
        $('#newMoneyTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
         var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        newMoneyTransferDataTable(keyword,fromDate,toDate,status);
      });
        
        
        
        function upiTransferDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var upiTransferDataTable = $('#upiTransferDataTable').DataTable({
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
            "url": "getUpiPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#upiTransferSearchBtn').on('click', function() {
        $('#upiTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        upiTransferDataTable(keyword,date);
      });







       function payoutOpenDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var payoutOpenDataTable = $('#payoutOpenDataTable').DataTable({
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
            "url": "payout/getPaymentList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }

       $('#payoutOpenSearchBtn').on('click', function() {
        $('#payoutOpenDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var date    = $("#date").val();
        payoutOpenDataTable(keyword,date);
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



        function moneyTransferHistoryDataTable(keyword = '',fromDate='', toDate = '' ,status = 0)
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
        var moneyTransferHistoryDataTable = $('#moneyTransferHistoryDataTable').DataTable({
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
            "url": "getMoneyTransferList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessIciciBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingIciciBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedIciciBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }

       $('#moneyTransferHistorySearchBtn').on('click', function() {
        $('#moneyTransferHistoryDataTable').DataTable().destroy();
         var keyword = $("#keyword").val();
        var fromDate    = $("#from_date").val();
        var toDate    = $("#to_date").val();
        var status    = $("#status").val();
        moneyTransferHistoryDataTable(keyword,fromDate,toDate,status);
      });



        function aepsTxnDataTable(keyword = '', from_date ='' ,to_date = '')
      { 

        var siteUrl = $("#siteUrl").val();
        var aepsTxnDataTable = $('#aepsTxnDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTransactionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+from_date+'|'+to_date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      } 



       function iciciAepsTxnDataTable(keyword = '', fromDate='', toDate = '' , status = 0,service = '')
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

         if(service == '')
        {
          var service = $("#service").val();
        }



        var siteUrl = $("#siteUrl").val();
        var iciciAepsTxnDataTable = $('#iciciAepsTxnDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getTransactionList",
              "data": function ( d ) {
               d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status+'|'+service;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessAepsBlock").html(json.successAmount+' / '+json.successRecord);            
            $("#totalFailedAepsBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      } 
      

      $('#iciciAepsHistorySearchBtn').on('click', function() {
      $('#iciciAepsTxnDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
       var status = $("#status").val();
       
        var service = $("#service").val();
      iciciAepsTxnDataTable(keyword,fromDate,toDate,status,service);
     });

      
      
      function nsdlPanDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var nsdlPanDataTable = $('#nsdlPanDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/report/getNsdlPanList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


      function nsdlPanCardDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var nsdlPanCardDataTable = $('#nsdlPanCardDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"distributor/report/getNsdlPanCardList",
              "data": function ( d ) {
              d.extra_search = keyword+"|"+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


      function utiPanRequestDataTable(keyword = '',fromDate='', toDate = '')
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
        var utiPanRequestDataTable = $('#utiPanRequestDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUtiBalanceList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          } 
          
        });
      
      }

      $('#utiBalanceSearchBtn').on('click', function() {
        $('#utiPanRequestDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        utiPanRequestDataTable(keyword,fromDate,toDate);
      }); 
      
      function settlementTransferDataTable(keyword = '',fromDate='', toDate = '' ,status = 0)
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
        var settlementTransferDataTable = $('#settlementTransferDataTable').DataTable({
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
            "url": "getNewPaymentList",
              "data": function ( d ) {
             d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessIciciBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingIciciBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedIciciBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }

       $('#settlementSearchBtn').on('click', function() {
        $('#settlementTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
         var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        settlementTransferDataTable(keyword,fromDate,toDate,status);
      });
    
    
    function settlementMoneyTransferDataTable(keyword = '',fromDate='', toDate = '' ,status = 0)
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
        var settlementMoneyTransferDataTable = $('#settlementMoneyTransferDataTable').DataTable({
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
            "url": "getSettlementList",
              "data": function ( d ) {
             d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessIciciBlock").html(json.successAmount+' / '+json.successRecord);
            $("#totalPendingIciciBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalFailedIciciBlock").html(json.failedAmount+' / '+json.failedRecord);
          }
            
          
        });
      

      
      }

       $('#settlementMoneySearchBtn').on('click', function() {
        $('#settlementMoneyTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
         var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        settlementMoneyTransferDataTable(keyword,fromDate,toDate,status);
      });
       


       function upiQrDataTable(keyword = '',fromDate='', toDate = '')
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

       
        var upiQrDataTable = $('#upiQrDataTable').DataTable({
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
            "url": "getUpiQrList",
              "data": function ( d ) {
               d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
              $("#totalSuccessIciciBlock").html(json.successAmount+' / '+json.successRecord);
            
          }
            
          
        });
      

      
      }


     $('#upiQrSearchBtn').on('click', function() {
      $('#upiQrDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
       var fromDate = $("#fromDate").val();
      var toDate = $("#toDate").val();
      
      upiQrDataTable(keyword,fromDate,toDate);
     });
       


    });
			
			
		
