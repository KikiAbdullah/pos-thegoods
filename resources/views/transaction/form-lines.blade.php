{!! Form::open(['route' => [$url, $id], 'method' => 'POST', 'id' => 'l-modal-form']) !!}

<div class="row mb-3">
    <label class="col-lg-2 col-form-label text-lg-end d-none d-lg-block">Package</label>
    <div class="col-lg-10">
        <ul class="nav nav-pills d-inline-flex">
            @foreach ($data['list_package'] as $package_id => $package)
                <li class="nav-item">
                    <a href="#" class="nav-link rounded-pill bg-success text-success px-3 m-1 bg-opacity-10"
                        onclick="changePackage({{ $package_id }}, this,'#mymodal')"
                        data-id="{{ $package_id }}">{{ $package }}</a>
                </li>
            @endforeach
        </ul>
        {!! Form::hidden('package_id', null, ['id' => 'package-id']) !!}
    </div>
</div>
<div class="row mb-3">
    <label class="col-lg-2 col-form-label text-lg-end d-none d-lg-block">Add On</label>
    <div class="col-lg-10">
        <div class="table-responsive">
            <table class="table table-xxs table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center" width="3%">#</th>
                        <th>Name</th>
                        <th class="text-end" width="15%">Harga</th>
                        <th class="text-end" width="15%">Qty</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @foreach ($data['list_addon'] as $addon)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $addon->name ?? '-' }}</td>
                            <td class="text-end">{{ cleanNumber($addon->harga) }}</td>
                            <td><input type="text" name="qty[{{ $addon->id }}]" class="form-control tform uang">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="d-flex justify-content-end align-items-center">
    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-start rounded-pill">
        <span class="btn-labeled-icon bg-black bg-opacity-20 m-1 rounded-pill">
            <i class="ph-plus-circle" id="submit_loader"></i>
        </span>
        Add Item
    </button>
</div>
{!! Form::close() !!}
