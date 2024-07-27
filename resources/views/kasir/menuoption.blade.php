@if (array_key_exists('print', $button))
    <a href="{{ route($button['print'], $id) }}" target="_blank"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold" onclick="BtnPrint(this, event)">
        <i class="ph-file-pdf  ph-2x text-danger"></i>
        Print PDF
    </a>
@endif

@if (array_key_exists('excel', $button))
    <a href="#" data-url="{{ route($button['excel'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold" onclick="BtnExport(this, event)">
        <i class="ph-microsoft-excel-logo   ph-2x text-success"></i>
        Export Excel
    </a>
@endif
