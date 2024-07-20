<html>

<head>
    <title>FORM SO</title>
    <style>
        @page {
            margin: 0.2cm;
        }

        body {
            font-size: 9pt;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0.2cm;
        }

        .title {
            font-size: 12pt;
            font-weight: bold;
        }

        p {
            margin: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .table-item thead tr th,
        .table-item tbody tr td {
            padding: 4px 8px;
        }

        .table-item thead {
            background: #ccc;
            border-bottom: 0.5px solid #000;
            border-top: 0.5px solid #000;
        }

        .table-item tbody {
            border: 0.5px solid #000;
        }

        .table-ttd tr td {
            border: 0.5px solid #000;
        }

        ul li {
            font-size: 0.655rem;
            list-style: decimal;
        }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td width="50%" valign="top">
                <img src="{{ public_path('app_local/img/logo.png') }}" width="100" style="margin-bottom: 10px;">
                <p>PT. Matahari Putra Makmur</p>
                <p>Jl. Raya Gempol - Bangil, Pasuruan</p>
            </td>
            <td width="30%" valign="top">
                <p>Print Time: {{ date('Y-m-d H:i:s') }}</p>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td valign="top" class="text-center">
                <div class="title"><u>FORM STOK OPNAME</u></div>
            </td>
        </tr>
    </table>
    <br />
    <div style="min-height: 200px;vertical-align: top;">
        <table width="100%" class="table-item" cellspacing="0" cellspadding="0" border="0.5">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Item Name</th>
                    <th width="5%" nowrap>Satuan</th>
                    <th width="15%" style="text-align: right;">Qty Web</th>
                    <th width="15%" style="text-align: right;">Qty Fisik</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    {{-- <div style="position:fixed;bottom:120px;"> --}}
    <div style="text-align:right;">
        Pasuruan, ..............................................<br />&nbsp;
        <table width="50%" class="table-ttd" cellspacing="0" cellspadding="0" align="right">
            <tr>
                <td width="25%" class="text-center">Warehouse</td>
                <td width="25%" class="text-center">Accounting</td>
            </tr>
            <tr>
                <td style="height: 60px;">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
    {{-- </div> --}}
</body>

</html>
