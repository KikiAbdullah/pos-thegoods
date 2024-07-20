{!! Form::open(['route' => $url, 'method' => 'POST', 'id' => 'l-modal-form']) !!}
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
