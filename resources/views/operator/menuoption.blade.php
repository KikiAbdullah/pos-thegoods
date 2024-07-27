@if (array_key_exists('upload-url-form', $button))
    <a href="#" data-url="{{ route($button['upload-url-form'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
        onclick="BtnUploadUrl(this, event)">
        <i class="ph-link-simple ph-2x text-indigo"></i>
        Upload URL
    </a>
@endif

@if (array_key_exists('payment', $button))
    <a href="#" data-url="{{ route($button['payment'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
        onclick="BtnOption('payment',this, event)">
        <i class="ph-arrow-u-up-right ph-2x text-success"></i>
        Pembayaran
    </a>
@endif
