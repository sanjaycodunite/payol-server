$(document).ready(function() {
			$('#example').DataTable();
      employeDataTable();
      closeClubDataTable();
      mdMemberDataTable();
      distributorMemberDataTable();
      retailerMemberDataTable();
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
      moneyTransferDataTable();
      moneyTransferHistoryDataTable();
      ticketDataTable();
      rechargeCommisionDataTable();

      complainDataTable();
      bbpsHistoryDataTable();
      topupHistoryDataTable();
      upiTxnDataTable();
      cashDepositeDataTable();
      upiCommisionDataTable();
      
   
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

        function cashDepositeDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var cashDepositeDataTable = $('#cashDepositeDataTable').DataTable({
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

       function upiCommisionDataTable(keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var upiCommisionDataTable = $('#upiCommisionDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"user/report/getUpiCommisionList",
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

       function upiTxnDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var upiTxnDataTable = $('#upiTxnDataTable').DataTable({
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

      function myWalletDataTable(keyword = '',member_id='',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var myWalletDataTable = $('#myWalletDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [{"targets": 1,"orderable": false}, {"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 6, "desc" ]],
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
            "url": siteUrl+"user/complain/getComplainList",
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

       function bbpsHistoryDataTable(status = 0,keyword = '',fromDate = '',toDate = '')
      { 
        if(status == 0)
        {
          var status = $("#status").val();
        }
        var siteUrl = $("#siteUrl").val();
        var bbpsHistoryDataTable = $('#bbpsHistoryDataTable').DataTable({
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false},{"targets": 6,"orderable": false},{"targets": 7,"orderable": false},{"targets": 8,"orderable": false},{"targets": 9,"orderable": false},{"targets": 10,"orderable": false},{"targets": 11,"orderable": false},{"targets": 12,"orderable": false},{"targets": 13,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"user/report/getBBPSHistoryList",
              "data": function ( d ) {
              d.extra_search = keyword+'|'+fromDate+'|'+toDate+'|'+status;
            }
          },
          "initComplete": function( settings, json ) {
            
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
          "pageLength": 50,
          "lengthMenu": [ 10, 25, 50, 75, 100 ],
          "searching": false,
          "columnDefs": [ {"targets": 0,"orderable": false},{"targets": 1,"orderable": false},{"targets": 2,"orderable": false},{"targets": 3,"orderable": false},{"targets": 4,"orderable": false},{"targets": 5,"orderable": false}],
          "processing": false ,
          "serverSide": true,
          "order": [[ 5, "desc" ]],
          deferRender: true,
          "ajax": {
            "url": siteUrl+"user/report/getRechargeCommisionList",
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





        function moneyTransferHistoryDataTable(keyword = '',date='')
      { 

        var siteUrl = $("#siteUrl").val();
        var moneyTransferHistoryDataTable = $('#moneyTransferHistoryDataTable').DataTable({
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


    });
			
			
		
