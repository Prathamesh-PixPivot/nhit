<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Letter - {{ $slNo }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .letter-title {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin-top: 15px;
        }
        
        .letter-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .letter-info div {
            flex: 1;
        }
        
        .letter-info strong {
            color: #2c3e50;
        }
        
        .bank-details {
            margin-bottom: 30px;
        }
        
        .bank-details h3 {
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .payments-table th,
        .payments-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .payments-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .payments-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
            color: #27ae60;
        }
        
        .total-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
        }
        
        .total-row td {
            border-top: 2px solid #27ae60;
        }
        
        .instructions {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .instructions h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .instructions ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 5px;
        }
        
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 200px;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            font-weight: bold;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">BANK LETTER</div>
    
    <div class="header">
        <div class="company-name">{{ config('app.name', 'NHIT') }}</div>
        <div class="company-address">
            Your Company Address Line 1<br>
            Your Company Address Line 2<br>
            Phone: +91-XXXXXXXXXX | Email: info@company.com
        </div>
        <div class="letter-title">RTGS/NEFT PAYMENT INSTRUCTION</div>
    </div>
    
    <div class="letter-info">
        <div>
            <strong>Letter No:</strong> {{ $slNo }}<br>
            <strong>Date:</strong> {{ now()->format('d/m/Y') }}<br>
            <strong>Total Payments:</strong> {{ $payments->count() }}
        </div>
        <div>
            <strong>Total Amount:</strong> ₹{{ number_format($totalAmount, 2) }}<br>
            <strong>Status:</strong> Approved<br>
            <strong>Generated By:</strong> {{ $payments->first()->user->name ?? 'System' }}
        </div>
    </div>
    
    <div class="bank-details">
        <h3>To: The Manager</h3>
        <p>
            <strong>Bank Name:</strong> [Bank Name]<br>
            <strong>Branch:</strong> [Branch Name]<br>
            <strong>Address:</strong> [Bank Address]
        </p>
        
        <p><strong>Subject:</strong> Request for RTGS/NEFT Transfer - {{ $slNo }}</p>
        
        <p>Dear Sir/Madam,</p>
        
        <p>
            We request you to kindly arrange RTGS/NEFT transfer for the following payments as per the details mentioned below. 
            Please debit our account and transfer the amounts to the respective beneficiary accounts.
        </p>
    </div>
    
    <table class="payments-table">
        <thead>
            <tr>
                <th style="width: 5%;">S.No.</th>
                <th style="width: 25%;">Beneficiary Name</th>
                <th style="width: 20%;">Account Number</th>
                <th style="width: 20%;">Bank & Branch</th>
                <th style="width: 10%;">IFSC Code</th>
                <th style="width: 15%;">Amount (₹)</th>
                <th style="width: 5%;">Purpose</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $payment->name_of_beneficiary ?? 'N/A' }}</strong>
                    @if($payment->project)
                        <br><small style="color: #666;">Project: {{ $payment->project }}</small>
                    @endif
                </td>
                <td>{{ $payment->account_number ?? 'N/A' }}</td>
                <td>
                    {{ $payment->name_of_bank ?? 'N/A' }}
                    @if($payment->name_of_bank)
                        <br><small style="color: #666;">Branch: Main Branch</small>
                    @endif
                </td>
                <td>{{ $payment->ifsc_code ?? 'N/A' }}</td>
                <td class="amount">{{ number_format($payment->amount, 2) }}</td>
                <td><small>{{ $payment->purpose ? (strlen($payment->purpose) > 20 ? substr($payment->purpose, 0, 20) . '...' : $payment->purpose) : 'Payment' }}</small></td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>TOTAL AMOUNT:</strong></td>
                <td class="amount" style="font-size: 14px;">{{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <div class="instructions">
        <h4>Payment Instructions:</h4>
        <ul>
            <li>Please process the above payments through RTGS/NEFT as per RBI guidelines</li>
            <li>Debit our Current Account No: [Your Account Number]</li>
            <li>Please send the UTR numbers and transaction details to our accounts department</li>
            <li>In case of any discrepancy, please contact us immediately</li>
            <li>This letter is system generated and approved by authorized personnel</li>
        </ul>
    </div>
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                Prepared By<br>
                <strong>{{ $payments->first()->user->name ?? 'System User' }}</strong><br>
                Accounts Department
            </div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line">
                Verified By<br>
                <strong>Finance Manager</strong><br>
                Finance Department
            </div>
        </div>
        
        <div class="signature-box">
            <div class="signature-line">
                Authorized Signatory<br>
                <strong>Director</strong><br>
                {{ config('app.name', 'Company Name') }}
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>
            This is a computer generated document. No signature is required.<br>
            Generated on: {{ now()->format('d/m/Y h:i A') }} | Letter No: {{ $slNo }}<br>
            For any queries, please contact: accounts@company.com | Phone: +91-XXXXXXXXXX
        </p>
    </div>
    
    <!-- Print Button (hidden in print) -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            <i class="bi bi-printer"></i> Print Letter
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Print functionality
        function printLetter() {
            window.print();
        }
        
        // Download as PDF functionality (requires additional setup)
        function downloadPDF() {
            // This would require a PDF generation library
            alert('PDF download functionality requires additional setup with libraries like Puppeteer or DomPDF');
        }
    </script>
</body>
</html>
