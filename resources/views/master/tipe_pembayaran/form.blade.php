<div class="row mb-3">
    <label class="col-lg-3 col-form-label text-lg-end d-none d-lg-block">Name</label>
    <div class="col-lg-9">
        {!! Form::text('name', null, [
            'class' => in_array('name', $errors->keys()) ? 'form-control is-invalid' : 'form-control',
            'placeholder' => 'Name',
        ]) !!}
    </div>
</div>