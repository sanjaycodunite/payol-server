$(document).ready(function() {
			$('#example').DataTable();
      employeDataTable();
      mdMemberDataTable();
      distributorMemberDataTable();
      retailerMemberDataTable();
      apiMemberDataTable();
      walletDataTable();
      creditDataTable();
      debitDataTable();
      fundRequestDataTable();
      apiRequestDataTable();
      myWalletDataTable();
      oldWalletDataTable();
      myUpiWalletDataTable();
      myFundRequestDataTable();
      rechargeDataTable();
      bbpsDataTable();
      manualMoneyTransferDataTable();
      moneyTransferDataTable();
      ticketDataTable();
      rechargeCommisionDataTable();

      complainDataTable();
      aepsKycDataTable();
      aepsHistoryDataTable();
      myAepsCommisionDataTable();
      payoutTransferDataTable();
      upiTransactionDataTable();
      payoutOpenDataTable();
      
   
      function employeDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var employeDataTable = $('#employeDataTable').DataTable({
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

       function myAepsCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var myAepsCommisionDataTable = $('#myAepsCommisionDataTable').DataTable({
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
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"portal/report/getMyAepsCommisionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });


       
      }


     $('#myAepsCommisionSearchBtn').on('click', function() {
      $('#myAepsCommisionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        myAepsCommisionDataTable(keyword,fromDate,toDate);
     });

       function aepsKycDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var aepsKycDataTable = $('#aepsKycDataTable').DataTable({
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

     function aepsHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var aepsHistoryDataTable = $('#aepsHistoryDataTable').DataTable({
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

      function mdMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var mdMemberDataTable = $('#mdMemberDataTable').DataTable({
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

      function apiMemberDataTable(keyword = '',date ='')
      { 

        var siteUrl = $("#siteUrl").val();
        var apiMemberDataTable = $('#apiMemberDataTable').DataTable({
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
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
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

     function myWalletDataTable(status = 0 , by=0 , keyword = '',member_id='',fromDate='', toDate = '')
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
            
              if(by == 0)
        {
          var by = $("#by").val();
        }


        var siteUrl = $("#siteUrl").val();
        var myWalletDataTable = $('#myWalletDataTable').DataTable({
           dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 100,
          "lengthMenu": [[18446744073, 100, 500, 50, ], ["All", 100, 500, 50]],
        //   "pageLength": 100,
        //   "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMyWalletList",
              "data": function ( d ) {
              //d.extra_search = status+'|'+keyword+"|"+member_id+'|'+fromDate+'|'+toDate;
              d.extra_search = status+'|'+by+'|'+keyword+'|'+member_id+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
             $("#totalCrBlock").html(json.crAmount+' / '+json.crRecord);
            //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalDrBlock").html(json.drAmount+' / '+json.drRecord);
          }
            
          
        });
      

      
      }
      $('#myWalletSearchBtn').on('click', function() {
        $('#myWalletDataTable').DataTable().destroy();
        var status = $("#status").val();        
         var by = $("#by").val();    
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
         var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
        myWalletDataTable(status,by,keyword,member_id,fromDate,toDate);
      }); 


       function oldWalletDataTable(status = 0 , by=0 , keyword = '',member_id='',fromDate='', toDate = '')
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
            
              if(by == 0)
        {
          var by = $("#by").val();
        }


        var siteUrl = $("#siteUrl").val();
        var oldWalletDataTable = $('#oldWalletDataTable').DataTable({
           dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 100,
          "lengthMenu": [[18446744073, 100, 500, 50, ], ["All", 100, 500, 50]],
        //   "pageLength": 100,
        //   "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getOldWalletList",
              "data": function ( d ) {
              //d.extra_search = status+'|'+keyword+"|"+member_id+'|'+fromDate+'|'+toDate;
              d.extra_search = status+'|'+by+'|'+keyword+'|'+member_id+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
             $("#totalCrBlock").html(json.crAmount+' / '+json.crRecord);
            //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalDrBlock").html(json.drAmount+' / '+json.drRecord);
          }
            
          
        });
      

      
      }
      $('#oldWalletSearchBtn').on('click', function() {
        $('#oldWalletDataTable').DataTable().destroy();
        var status = $("#status").val();        
         var by = $("#by").val();    
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
         var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
        oldWalletDataTable(status,by,keyword,member_id,fromDate,toDate);
      }); 


      function myUpiWalletDataTable(status = 0 , by=0 , keyword = '',member_id='',fromDate='', toDate = '')
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
            
              if(by == 0)
        {
          var by = $("#by").val();
        }


        var siteUrl = $("#siteUrl").val();
        var myUpiWalletDataTable = $('#myUpiWalletDataTable').DataTable({
           dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 100,
          "lengthMenu": [[18446744073, 100, 500, 50, ], ["All", 100, 500, 50]],
        //   "pageLength": 100,
        //   "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getMyUpiWalletList",
              "data": function ( d ) {
              //d.extra_search = status+'|'+keyword+"|"+member_id+'|'+fromDate+'|'+toDate;
              d.extra_search = status+'|'+by+'|'+keyword+'|'+member_id+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
             $("#totalCrBlock").html(json.crAmount+' / '+json.crRecord);
            //$("#totalPendingRechargeBlock").html(json.pendingAmount+' / '+json.pendingRecord);
            $("#totalDrBlock").html(json.drAmount+' / '+json.drRecord);
          }
            
          
        });
      

      
      }
      $('#myUpiWalletSearchBtn').on('click', function() {
        $('#myUpiWalletDataTable').DataTable().destroy();
        var status = $("#status").val();        
         var by = $("#by").val();    
        var keyword = $("#keyword").val();
        var member_id = $("#member_id").val();
         var fromDate = $("#from_date").val();
      var toDate = $("#to_date").val();
        myUpiWalletDataTable(status,by,keyword,member_id,fromDate,toDate);
      }); 


      function creditDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var creditDataTable = $('#creditDataTable').DataTable({
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


     function apiRequestDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var apiRequestDataTable = $('#apiRequestDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 8, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getApiRequestList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#apiSearchRequestBtn').on('click', function() {
      $('#apiRequestDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      apiRequestDataTable(keyword,date);
     });



     function myFundRequestDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var myFundRequestDataTable = $('#myFundRequestDataTable').DataTable({
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

     function rechargeDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var rechargeDataTable = $('#rechargeDataTable').DataTable({
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
              d.extra_search = keyword+'|'+date;
            }
          },
          "initComplete": function( settings, json ) {
            
          }
            
          
        });
      

      
      }


     $('#rechargeSearchBtn').on('click', function() {
      $('#rechargeDataTable').DataTable().destroy();
      var keyword = $("#keyword").val();
      var date = $("#date").val();
      rechargeDataTable(keyword,date);
     });

     function complainDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var complainDataTable = $('#complainDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false}, {"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"portal/complain/getComplainList",
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

     function rechargeCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var rechargeCommisionDataTable = $('#rechargeCommisionDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"portal/report/getRechargeCommisionList",
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


     function bbpsDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var bbpsDataTable = $('#bbpsDataTable').DataTable({
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


       function moneyTransferDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var moneyTransferDataTable = $('#moneyTransferDataTable').DataTable({
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



       function payoutTransferDataTable(keyword = '',fromDate='', toDate = '' , status = 0)
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
        var payoutTransferDataTable = $('#payoutTransferDataTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
              'csv', 'excel'
          ],
          "lengthMenu": [[18446744073, 100, 500, 50, ], ["All", 100, 500, 50]],
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
            
          }
            
          
        });
      

      
      }

       $('#payoutSearchBtn').on('click', function() {
        $('#payoutTransferDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var status = $("#status").val();
        payoutTransferDataTable(keyword,fromDate,toDate,status);
      });

       function upiTransactionDataTable(keyword = '',fromDate = '',toDate = '')
      { 

        var siteUrl = $("#siteUrl").val();
        if(fromDate == '' && toDate == '')
        {
          var fromDate = $("#from_date").val();
          var toDate = $("#to_date").val();
        }
        var upiTransactionDataTable = $('#upiTransactionDataTable').DataTable({
          dom: 'lBrtip',
          buttons: [
              'csv', 'excel'
          ],
          "pageLength": 50,
          "lengthMenu": [[10, 25, 50, 4294967295, ], [10, 25, 50, "All"]],
          "searching": false,
          "columnDefs": [{"targets": 0,"orderable": false},{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 0, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": "getUpiTransactionList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate;
            }
          },
          "initComplete": function( settings, json ) {
            $("#totalSuccessRechargeBlock").html(json.totalSuccess);
            $("#totalFailedRechargeBlock").html(json.totalFailed);
            $("#totalSuccessChargeBlock").html(json.totalCharge);
          }
            
          
        });
      

      
      }
      $('#upiTransactionSearchBtn').on('click', function() {
        $('#upiTransactionDataTable').DataTable().destroy();
        var keyword = $("#keyword").val();
        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        upiTransactionDataTable(keyword,fromDate,toDate);
      });



    });
			
			
		
