<div class="card">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0">Edit {{ $title }}</h6>
    </div>

    <div class="card-body">
        {!! Form::model($item, ['route' => [$url['update'], $item->id], 'method' => 'PUT', 'id' => 'formupdate']) !!}
        @include($form)
        <div class="d-flex justify-content-end align-items-center">
            <button type="submit" class="btn btn-secondary btn-labeled btn-labeled-start rounded-pill">
                <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
                    <i class="ph-check"></i>
                </span>
                Update
            </button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
