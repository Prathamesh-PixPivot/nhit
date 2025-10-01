
                        <html>

                        <head></head>

                        <body>
                                <table style="border: 1px solid #ddd;">
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">To</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Mr Sujai Idnani,Vice President
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">State Bank of India Mutual Fund
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">New Delhi</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Dear Sir,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5"><strong>Sub : Purchase of Units
                                                of
                                                SBI Overnight Mutual Funds<strong></td>
                                    <tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">With reference to the above
                                            matter
                                            we would like to purchase units for <strong>Rs. 0.00</strong> No Rupees Only
                                            under
                                            SBI OVERNIGHT FUND against the following folio numbers:-
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">Sr No</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">From Investment Account Number
                                        </td>
                                        <td style="border: 1px solid #ddd;" colspan="1">From Project Name</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Folio Number</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Amount</td>
                                    </tr>
                                    @if (!empty($data) && $data->isNotEmpty())
                                    @php
                                        $i = 1;
                                        $sum_tot_price = 0;
                                    @endphp
                                    @foreach ($data as $row)
                                    @php
                                        $sum_tot_price += $row->amount;
                                    @endphp
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">{{$i}}</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">{{ $row->full_account_number ?? 'N/A' }}</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">{{ $row->project ?? 'N/A' }}</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Folio Number</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">{{ $row->amount ?? 'N/A' }}</td>
                                    </tr>
                                    @php
                                    $i++;
                                @endphp
                            @endforeach
                        @else
                            <tr>
                                <td style="border: 1px solid #ddd; text-align:center" colspan="7"><strong>No record found</strong>
                                </td>
                            </tr>
                        @endif
                                    
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="4">Total</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"> <strong>{{ !empty($data) && $data->isNotEmpty() ? $sum_tot_price : '0.0' }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">We are effecting the fund
                                            transfer
                                            for the above in following account:-
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">Beneficiary Name</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">SBI OVERNIGHT FUND</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">Beneficiary A/c Number</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">SBI OVERNIGHT FUND</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">IFSC Code</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">SBIN0000300</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Kindly effect the mutual fund
                                            transaction.</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Thanking you,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Yours Sincerely,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">For National Highways Infra
                                            Projects Private Limited</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="2"><br><br><br><br>Auth. Signatory
                                        </td>
                                        <td style="border: 1px solid #ddd;" colspan="3"><br><br><br><br>Auth. Signatory
                                        </td>
                                    </tr>
                                </table>
                                <div class="row mb-3 mt-3" bis_skin_checked="1">
                                    <div class="col-sm-10" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-primary">Generate PDF</button>
                                    </div>
                                </div>
                        </body>

                        </html>
