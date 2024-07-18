<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Name</label>
    <div class="col-lg-9">
        {!! Form::text('name', null, [
            'class' => in_array('name', $errors->keys()) ? 'form-control is-invalid' : 'form-control',
            'placeholder' => 'Name',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Harga [Rp]</label>
    <div class="col-lg-9">
        {!! Form::text('harga', null, [
            'class' => in_array('harga', $errors->keys()) ? 'form-control is-invalid' : 'form-control',
            'placeholder' => 'Harga',
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Description</label>
    <div class="col-lg-9">
        {!! Form::textarea('description', null, [
            'class' => in_array('description', $errors->keys()) ? 'form-control is-invalid' : 'form-control',
            'placeholder' => 'Description',
            'rows' => 2,
        ]) !!}
    </div>
</div>

<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Status</label>
    <div class="col-lg-9">
        {!! Form::select('status', ['1' => 'Show', '0' => 'Hide'], null, [
            'class' => 'select',
            'placeholder' => 'Status',
        ]) !!}
    </div>
</div>
