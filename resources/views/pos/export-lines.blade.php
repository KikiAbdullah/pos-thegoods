<table>
    <thead>
        <tr>
            <th colspan="2">No: </th>
            <th colspan="4"><b>{{ $item->no }}</b></th>
        </tr>
        <tr>
            <th colspan="2">Tanggal: </th>
            <th colspan="4"><b>{{ formatDate('Y-m-d', 'd/m/Y', $item->tanggal) }}</b></th>
        </tr>
        <tr>
            <th colspan="2">Keterangan: </th>
            <th colspan="4"><i>{{ $item->text }}</i></th>
        </tr>
        <tr>
            <th colspan="2">Created By: </th>
            <th colspan="4"><b>{{ $item->createdBy->name ?? '' }}</b></th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th><b>#</b></th>
            <th width="150px"><b>No</b></th>
            <th width="150px"><b>Rak Asal</b></th>
            <th width="150px"><b>Rak Tujuan</b></th>
            <th width="150px"><b>Item ID</b></th>
            <th width="300px"><b>Item Name</b></th>
            <th><b>Qty</b></th>
            <th><b>Satuan</b></th>
        </tr>
    </thead>
    <tbody id="tbody">
        @forelse ($item->packages as $a)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->no }}</td>
                <td>{{ $item->asalRak->no }}</td>
                <td>{{ $a->rak->no }}</td>
                <td>{{ $a->item_id }}</td>
                <td>{{ $a->item_name }}</td>
                <td>{{ cleanNumber($a->qty) }}</td>
                <td>{{ $a->satuan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Tidak Ada</td>
            </tr>
        @endforelse
    </tbody>
</table>
