<!DOCTYPE html>
<html>

<head>
    <title>Settlement Invoice</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap" rel="stylesheet">
    <style>
    @media print {
        .btn {
            display: none;
            /* Hide buttons when printing */
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            /* Preserve colors */
        }
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Open Sans', sans-serif;
        background-color: #f5f5f5;
    }

    .container {
        width: 21cm;
        /* A4 width */
        height: 29.7cm;
        /* A4 height */
        margin: 30px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    caption {
        padding: 10px;
        text-align: center;
    }

    img {
        max-width: 200px;
        height: auto;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f9f9f9;
        color: #333;
    }

    td {
        background-color: #fff;
        color: #333;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header img {
        max-width: 150px;
    }

    .title {
        font-size: 24px;
        margin: 0;
        color: #334;
    }

    .date {
        font-size: 16px;
        color: #667;
    }

    .status {
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
        text-align: center;
    }

    .status.pending {
        color: #ff9800;
        background-color: #fff3e0;
    }

    .status.success {
        color: #4caf50;
        background-color: #e8f5e9;
    }

    .status.failed {
        color: #f44336;
        background-color: #fdecea;
    }

    .footer {
        border-top: 2px solid #ddd;
        padding: 10px 0;
        text-align: center;
        color: #666;
    }

    .footer h4 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    .footer p {
        margin: 5px 0;
        font-size: 14px;
    }

    .btn {
        display: inline-block;
        font-size: 14px;
        color: #ffffff;
        background-color: #2196f3;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        margin: 10px 5px;
        text-align: center;
    }

    .btn:hover {
        background-color: #1976d2;
    }

    .btn.print {
        background-color: #4caf50;
    }

    .btn.print:hover {
        background-color: #388e3c;
    }

    .black {
        color: #000000;
    }

    .footerImg {
        max-width: 150px;
    }

    .green {
        color: 008000;
    }

    .red {
        color: #ff0000;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{site_url}<?php echo $accountData['image_path']; ?>" alt="Payol">
            <div>
                <h3 class="title">Invoice #<?php echo $detail['invoice_no']; ?></h3>
                <div class="date black">Date: <?php echo date('d M Y', strtotime($detail['created'])); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th colspan="2">Transaction Details:</th>
                </tr>
                <tr>
                    <th>Member ID</th>
                    <th><?php echo $detail['memberID']; ?></th>
                </tr>
                <tr>
                    <th>Beneficiary Name</th>
                    <th><?php echo $detail['account_holder_name']; ?></th>
                </tr>
                <tr>
                    <th>Account Number</th>
                    <th><?php echo $detail['account_no']; ?></th>
                </tr>
                <tr>
                    <th>IFSC Code</th>
                    <th><?php echo $detail['ifsc']; ?></th>
                </tr>
                <tr>
                    <th>Transaction ID</th>
                    <th><?php echo $detail['transaction_id']; ?></th>
                </tr>
                <tr>
                    <th>RRN</th>
                    <th><?php echo $detail['rrn'] ?: 'N/A'; ?></th>
                </tr>
                <tr>
                    <th>Time</th>
                    <th><?php echo date('d-m-Y h:i A', strtotime($detail['created'])); ?></th>
                </tr>
                <tr>
                    <th>Status</th>
                    <th>
                        <?php
                        switch ($detail['status']) {
                            case 1:
                                echo "<span>Processing</span>";
                                break;
                            case 2:
                                echo "<span style='color: #03a9f4; font-weight: bold;'>Pending</span>";
                                break;
                            case 3:
                                echo "<span style='color: green; font-weight: bold;'>Success</span>";
                                break;
                            case 4:
                                echo "<span style='color: red; font-weight: bold;'>Failed</span>";
                                break;
                            default:
                                echo "<span>Status Unknown</span>";
                                break;
                        }
                        ?>
                    </th>
                </tr>
                <tr colspan="2">
                    <th>Transfer Amount (Rs)</th>
                    <th><?php echo number_format($detail['transfer_amount'], 2); ?> /-</th>
                </tr>
                <tr colspan="2">
                    <th>Charge Amount (Rs)</th>
                    <th><?php echo number_format($detail['transfer_charge_amount'], 2); ?> /-</th>
                </tr>
                <tr colspan="2">
                    <th>Net Amount (Rs)</th>
                    <th><?php echo number_format($detail['total_wallet_charge'], 2); ?> /-</th>
                </tr>
            </thead>
        </table>

        <div class="footer">
            <img src="{site_url}<?php echo $accountData['image_path']; ?>" class="footerImg" alt="Payol">
            <p class="text-center black"><b>Near Head Office Hazaribagh Jharkhand 825301</b></p>
            <a href="javascript:window.print()" class="print green wpb">Print</a> &nbsp;
            <a href="javascript:window.close()" class="red wpb">Close</a>
        </div>
    </div>
    <script>
    // Hide buttons before printing
    window.onbeforeprint = function() {
        const buttons = document.querySelectorAll('.wpb');
        buttons.forEach(button => {
            button.style.display = 'none';
        });
    };

    // Optional: Restore buttons after printing
    window.onafterprint = function() {
        const buttons = document.querySelectorAll('.wpb');
        buttons.forEach(button => {
            button.style.display = '';
        });
    };
    </script>
</body>

</html>