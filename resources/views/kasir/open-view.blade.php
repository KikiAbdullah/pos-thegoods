{!! Form::open(['route' => $url, 'method' => 'POST', 'id' => 'l-modal-form']) !!}
<div class="row">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Tanggal</label>
    <div class="col-lg-3  mb-3 ">
        {!! Form::text('tanggal', date('d-m-Y'), [
            'class' => 'form-control',
            'placeholder' => 'Tanggal',
            'disabled',
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
        ]) !!}
        <cite class="fs-sm text-muted">Saldo Awal adalah nominal yang dipegang kasir untuk kembalian</cite>

    </div>

    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('siteurl') }}" class="me-3">
            <i class="ph-house"></i>
        </a>
        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
            <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
                <i class="ph-paper-plane-tilt submit_loader"></i>
            </span>
            Submit
        </button>
    </div>
    {!! Form::close() !!}
