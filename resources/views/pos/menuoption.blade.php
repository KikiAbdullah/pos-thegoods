@can('transaction_verify')
    @if (array_key_exists('verify', $button))
        <a href="#" data-url="{{ route($button['verify'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('verify',this, event)">
            <i class="ph-check-circle ph-2x text-success"></i>
            Verifikasi
        </a>
    @endif
@endcan

@can('transaction_unverify')
    @if (array_key_exists('unverify', $button))
        <a href="#" data-url="{{ route($button['unverify'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('unverify',this, event)">
            <i class="ph-x-circle ph-2x text-danger"></i>
            Batal Verifikasi
        </a>
    @endif
@endcan

@if (array_key_exists('upload-url-form', $button))
    <a href="#" data-url="{{ route($button['upload-url-form'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
        onclick="BtnUploadUrl(this, event)">
        <i class="ph-link-simple ph-2x text-indigo"></i>
        Upload URL
    </a>
@endif

@can('transaction_payment')
    @if (array_key_exists('payment', $button))
        <a href="#" data-url="{{ route($button['payment'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('payment',this, event)">
            <i class="ph-arrow-u-up-right ph-2x text-success"></i>
            Pembayaran
        </a>
    @endif
@endcan

@can('transaction_unpayment')
    @if (array_key_exists('unpayment', $button))
        <a href="#" data-url="{{ route($button['unpayment'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('unpayment',this, event)">
            <i class="ph-arrow-u-up-left ph-2x text-danger"></i>
            Batal Pembayaran
        </a>
    @endif
@endcan

@can('transaction_ordered')
    @if (array_key_exists('ordered', $button))
        <a href="#" data-url="{{ route($button['ordered'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('ordered',this, event)">
            <i class="ph-arrow-u-up-right ph-2x text-success"></i>
            Order
        </a>
    @endif
@endcan

@can('transaction_unordered')
    @if (array_key_exists('unordered', $button))
        <a href="#" data-url="{{ route($button['unordered'], $id) }}"
            class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold"
            onclick="BtnOption('unordered',this, event)">
            <i class="ph-arrow-u-up-left ph-2x text-danger"></i>
            Batal Order
        </a>
    @endif
@endcan


@if (array_key_exists('vedit', $button))
    <a href="{{ route($button['vedit'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold editBtn">
        <i class="ph-pencil-simple-line ph-2x text-indigo"></i>
        VIEW/EDIT
    </a>
@endif

@if (array_key_exists('edit', $button))
    <a href="{{ route($button['edit'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold editBtn">
        <i class="ph-pencil-simple-line ph-2x text-indigo"></i>
        EDIT
    </a>
@endif

@if (array_key_exists('show', $button))
    <a href="{{ route($button['show'], $id) }}"
        class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold btnShow">
        <i class="ph-magnifying-glass ph-2x text-indigo"></i>
        SHOW
    </a>
@endif

@if (array_key_exists('destroy', $button))
    {!! Form::open([
        'route' => [$button['destroy'], $id],
        'method' => 'DELETE',
        'class' => 'delete',
        'style' => 'display: contents',
    ]) !!}
    <a href="#" class="btn flex-column btn-float py-2 mx-2 text-uppercase text-dark fw-semibold deleteBtn">
        <i class="ph-trash ph-2x text-danger"></i>
        DELETE
    </a>
    {!! Form::close() !!}
@endif
