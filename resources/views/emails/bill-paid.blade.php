<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bill Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .bill-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
        <p>Flat & Bill Management System</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $flat->owner_name }},</p>
        
        <p>This is to confirm that your bill payment has been received and processed successfully.</p>
        
        <div class="bill-details">
            <h3>Payment Details</h3>
            <p><strong>Flat:</strong> {{ $flat->flat_number }}</p>
            <p><strong>Building:</strong> {{ $building->name }}</p>
            <p><strong>Category:</strong> {{ $billCategory->name }}</p>
            <p><strong>Month:</strong> {{ $bill->month }}</p>
            <p><strong>Amount Paid:</strong> <span class="amount">${{ number_format($bill->amount, 2) }}</span></p>
            @if($bill->due_amount > 0)
                <p><strong>Previous Due Amount:</strong> ${{ number_format($bill->due_amount, 2) }}</p>
            @endif
            <p><strong>Payment Date:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
        
        <p>Thank you for your prompt payment. Your account is now up to date.</p>
        
        <p>If you have any questions, please contact the building management.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the Flat & Bill Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>




