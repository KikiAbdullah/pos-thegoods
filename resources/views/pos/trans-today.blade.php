<div class="row mx-1">
    @foreach ($items as $transToday)
        <div class="card mx-1 col-md-3" style="border-radius: 10px;">
            <a href="#" onclick="ChooseTrans({{ $transToday->id }})" class="d-flex align-items-center text-body lh-1 py-sm-2 p-2">
                <i class="ph-user ph-2x text-success me-2"></i>
                <div class="">
                    <div class="fs-sm text-muted mb-1">#{{ $transToday->no }}</div>
                    <div class="fw-semibold">{{ ucwords($transToday->customer_name) }}</div>
                </div>
                <div class="ms-auto">
                    {!! $transToday->status_formatted !!}
                </div>
            </a>
        </div>
    @endforeach
</div>
