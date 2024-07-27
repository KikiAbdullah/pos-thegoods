<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Add {{ $title }}</h6>
    </div>

    <div class="card-body">
        {!! Form::open(['route' => $url['store'], 'method' => 'POST', 'id' => 'dform']) !!}
        @include($form)
        <div class="d-flex justify-content-end align-items-center">
            <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
                <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
                    <i class="ph-paper-plane-tilt submit_loader"></i>
                </span>
                Submit
            </button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
