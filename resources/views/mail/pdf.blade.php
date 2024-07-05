<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>
<body>
    <div style="display: block; width: 100%;">
        <div style="border: 1px solid #0076D7; width: 100%; max-width: 600px; padding: 30px; margin: 0 auto;">
            <table cellspacing="0" style="width:100%; max-width: 100%; border: 0; background-color: #fff;">
                <tbody>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width:100%; border: 0; background-color: #fff;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 5px;">
                                            <img src="https://milaapp.in/frontend/assets/images/logo.png" alt="Milaap" style="width:120px; height: auto;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 900; color: #0076D7; text-align: center; padding: 10px 5px;">TAX INVOICE</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width:100%; border: 0; background-color: #fff;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>Invoice Date : </strong> {{date('d-M-Y')}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>Invoice Number : </strong> {{$transaction->unique_id}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width:100%; border: 0; background-color: #fff;">
                                <thead>
                                    <tr>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 600; text-decoration: underline; text-align: left; padding: 5px;">
                                            Customer Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>Name of Customer : </strong> {{$user->name}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>Address : </strong> {{$user->address}}, {{$user->city}},{{$user->state}}, India
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>Pincode : </strong> {{$user->pincode}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 500; padding: 5px;">
                                            <strong>GST : </strong> 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width: 100%; border: 1px solid #000; background-color: #fff;">
                                <thead>
                                    <tr>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Sl. No.</th>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Particulars - Package name</th>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Rate</th>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000; padding: 5px;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $taxRate = 18;
                                        $taxableAmount = $transaction->amount / (1 + ($taxRate / 100));
                                        $gstAmount = $transaction->amount - $taxableAmount;
                                    @endphp
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">1</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">{{$package}}</td>
                                        <td style="border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000;">{{ number_format($transaction->actual_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Negotiable Amount</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000;">{{ number_format($transaction->negotiable_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Paid Amount</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000;">{{ number_format($transaction->amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">Taxable Amount</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-bottom: 1px solid #000;">{{number_format($taxableAmount, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 5px;">IGST</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000; border-right: 1px solid #000;">{{$taxRate}}%</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-bottom: 1px solid #000;">{{number_format($gstAmount, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: center; border-right: 1px solid #000; padding: 5px;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right; border-right: 1px solid #000; padding: 5px;">Total Amount</td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; text-align: right; border-right: 1px solid #000;"></td>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; text-align: right;">{{ number_format($transaction->amount, 2, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width: 100%; border: 0; background-color: #fff;">
                                <tbody>
                                    <tr>
                                        <td valign="top">
                                            <table cellspacing="0" style="width: 100%; border: 0; background-color: #fff;">
                                                <tbody>
                                                    <tr>
                                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; padding: 10px 5px;">Whether the tax is payable on reverse charge basis: No</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; padding: 10px 5px;"><strong>IGST : </strong> {{$taxRate}}%</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 400; padding: 10px 5px;"><strong>HSN : </strong> </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td valign="top">
                                            <table cellspacing="0" style="width: 100%; border: 0; background-color: #fff;">
                                                <tbody>
                                                    <tr>
                                                        <td style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 700; text-align: right; padding: 10px 5px;">Authorised Signatory</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 20px 0px;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 700; text-align: right; padding: 10px 5px;">
                                                            <p style="margin: 0;">Name: Gaurav Sharma</p>
                                                            <p style="margin: 0;">Designation: Senior Manager</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width: 100%; border: 0; background-color: #fff;">
                                <thead>
                                    <tr>
                                        <th style="font-family: 'Poppins', sans-serif; font-size: 14px; font-weight: 600; text-decoration: underline; text-align: left; padding: 5px;">NOTE:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 400;">
                                            <ul style="padding: 0 0 0 16px; margin: 0;">
                                                <li style="line-height: 20px;">Tenure of service and payment trems for this invoice would be governed as per the agreement between the Customer and Milaap.</li>
                                                <li style="line-height: 20px;">This invoice is valid, subjct to realization of due payments, as mentioned in details above.</li>
                                                <li style="line-height: 20px;">Any payment made is covered under "Advrtising Contract" u/s 194C. TDS, if applicable, shall be @ 2%.</li>
                                                <li style="line-height: 20px;">You are requested to validate this invoice along with GSTIN within one month of invoice date.</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td style="background-color: #0076D7; padding: 2px 0;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0;"></td>
                    </tr>
                    <tr>
                        <td>
                            <table cellspacing="0" style="width: 100%; border: 0; background-color: #fff;">
                                <tbody>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 400;"><strong>Milaap.</strong> 6th floor, Tower 2, Assotech Business Cresterra, Plot No.22, Sec 135, Noida - 201305, Uttar Pradesh, India, Ph. No: - +91 - 120 - 6777777</td>
                                    </tr>
                                    <tr>
                                        <td style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 400;">PAN No.: AAACI5853L, GSTIN No.: 09AAACI5853L2Z5, CIN: U74899DL1999PLC101534</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>