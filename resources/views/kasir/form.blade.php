<div class="row">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Tanggal</label>
    <div class="col-lg-3  mb-3 ">
        {!! Form::text('tanggal', date('d-m-Y'), [
            'class' => 'form-control',
            'placeholder' => 'Tanggal',
            'readonly',
        ]) !!}
    </div>
    <label class="col-lg-2 col-form-label text-lg-end d-none d-lg-block">Kasir</label>
    <div class="col-lg-4 mb-3">
        {!! Form::text('created_name', auth()->user()->name, [
            'class' => 'form-control',
            'placeholder' => 'Kasir',
            'disabled',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Saldo Awal</label>
    <div class="col-lg-9">
        {!! Form::text('saldo_awal', null, [
            'class' => 'form-control uang',
            'placeholder' => 'Saldo Awal',
            'disabled' => isset($item) ? true : false,
        ]) !!}
        @if (!isset($item))
            <cite class="fs-sm text-muted">Saldo Awal adalah nominal yang dipegang kasir untuk kembalian</cite>
        @endif
    </div>
</div>
