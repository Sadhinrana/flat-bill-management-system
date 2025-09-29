<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Bill Created</title>
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
            background-color: #007bff;
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
            color: #dc3545;
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
        <h1>New Bill Created</h1>
        <p>Flat & Bill Management System</p>
    </div>
    
    <div class="content">
        <p>Dear {{ $flat->owner_name }},</p>
        
        <p>A new bill has been created for your flat <strong>{{ $flat->flat_number }}</strong> in {{ $building->name }}.</p>
        
        <div class="bill-details">
            <h3>Bill Details</h3>
            <p><strong>Category:</strong> {{ $billCategory->name }}</p>
            <p><strong>Month:</strong> {{ $bill->month }}</p>
            <p><strong>Amount:</strong> <span class="amount">${{ number_format($bill->amount, 2) }}</span></p>
            @if($bill->due_amount > 0)
                <p><strong>Due Amount:</strong> <span class="amount">${{ number_format($bill->due_amount, 2) }}</span></p>
            @endif
            @if($bill->notes)
                <p><strong>Notes:</strong> {{ $bill->notes }}</p>
            @endif
        </div>
        
        <p>Please ensure payment is made by the due date to avoid any late fees.</p>
        
        <p>If you have any questions, please contact the building management.</p>
        
        <p>Thank you for your prompt attention to this matter.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the Flat & Bill Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>




