{!! Form::open(['route' => [$url, $id], 'method' => 'POST', 'id' => 'l-upload-form']) !!}

<div class="table-responsive mb-3">
    <table class="table table-xxs table-bordered table-striped">
        <thead>
            <tr>
                <th class="text-center" width="3%">#</th>
                <th width="20%">Package</th>
                <th>URL</th>
            </tr>
        </thead>
        <tbody id="tbody">
            @foreach ($item->packages as $package)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $package->package_name ?? '-' }}</td>
                    <td>
                        {!! Form::text("url[$package->id]", $package->url, [
                            'class' => 'form-control tform',
                            'placeholder' => 'URL Google Drive',
                        ]) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<div class="d-flex justify-content-end align-items-center">
    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
        <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
            <i class="ph-plus-circle" id="submit_loader"></i>
        </span>
        Add URL
    </button>
</div>
{!! Form::close() !!}
