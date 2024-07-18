<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Tanggal</label>
    <div class="col-lg-9">
        {!! Form::text('tanggal', isset($item) ? $item->tanggal_formatted : null, [
            'class' => 'form-control daterange-single',
            'placeholder' => 'Tanggal',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Cust Name</label>
    <div class="col-lg-9">
        {!! Form::text('customer_name', null, [
            'class' => 'form-control',
            'placeholder' => 'Cust Name',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Cust Whatsapp</label>
    <div class="col-lg-9">
        {!! Form::text('customer_whatsapp', null, [
            'class' => 'form-control',
            'placeholder' => 'Cust Whatsapp',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Keterangan</label>
    <div class="col-lg-9">
        {!! Form::textarea('text', null, ['class' => 'form-control', 'placeholder' => 'Keterangan', 'rows' => 2]) !!}
    </div>
</div>
