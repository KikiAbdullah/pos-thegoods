{!! Form::open(['route' => $url, 'method' => 'POST', 'id' => 'l-modal-form']) !!}

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Tipe Pembayaran</label>
    <div class="col-lg-9">
        {!! Form::select('tipe_pembayaran_id', $data['list_tipe_pembayaran'], null, [
            'class' => 'select',
            'placeholder' => 'Tipe Pembayaran',
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
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Email</label>
    <div class="col-lg-9">
        {!! Form::text('customer_email', null, [
            'class' => 'form-control',
            'placeholder' => 'Email',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Keterangan</label>
    <div class="col-lg-9">
        {!! Form::textarea('text', null, ['class' => 'form-control', 'placeholder' => 'Keterangan', 'rows' => 2]) !!}
    </div>
</div>

<div class="d-flex justify-content-end align-items-center">
    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
        <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
            <i class="ph-paper-plane-tilt submit_loader"></i>
        </span>
        Submit
    </button>
</div>
{!! Form::close() !!}
