@extends('layouts.header')

@section('customcss')
@endsection

@section('content')
    <!-- Page header -->
    <div class="page-header">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="page-title">
                    <h4 class="fw-semibold">{{ $title ?? '' }}</h4>
                    <div class="text-muted">{{ $subtitle ?? '' }}</div>
                </div>

                <a href="#page_header"
                    class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>

            <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page_header">
                <div class="hstack gap-0 mb-3 mb-lg-0">

                    <span class="menuoption"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- /page header -->


    <!-- Content area -->
    <div class="content pt-0">
        @include('layouts.alert')
        <div class="row">
            <div class="col-md-6">
                <!-- Card -->
                <div class="card w-100 table-responsive">
                    <table class="table table-xxs" id="dtable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Open</th>
                                <th>Closed</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- /card -->
            </div>
            <div class="col-md-6" id="dynamic-form">
            </div>
            <div class="col-md-6" id="dynamic-form-2">
            </div>

        </div>

    </div>
    <!-- /content area -->
@endsection

@section('customjs')
    <script src="{{ asset('app_local/js/swal.js') }}"></script>
    <script type="text/javascript">
        var dtable;
        const urlAjax = '{{ route('kasir.get-data') }}';
        var lines = '{{ route('kasir.lines') }}';
        const getButtonOption = '{{ route('kasir.menuoption') }}';
        const buttons = "";

        $(document).ready(function($) {
            dtable = $('#dtable').DataTable({
                "select": {
                    style: "single",
                    info: false
                },
                "serverSide": true,
                "stateSave": true,
                "sServerMethod": "GET",
                "deferRender": true,
                "rowId": 'id',
                "ajax": urlAjax,
                "columns": [{
                        data: 'id'
                    },
                    {
                        data: 'tanggal_formatted'
                    },
                    {
                        data: 'created_name'
                    },
                    {
                        data: 'open'
                    },
                    {
                        data: 'closed'
                    },
                    {
                        data: 'status_formatted'
                    },
                ],
                "order": [
                    [0, "desc"]
                ],
                "dom": '<"datatable-header"Bf<"toolbar">><"datatable-scroll"t><"datatable-footer"lp>',
                "columnDefs": [{
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }, ],
            });
            //set class for page length
            $("#dtable_length").addClass('d-none d-lg-block');

            $("div.toolbar").html(
                '<label class="ps-2">' +
                '{!! Form::select(
                    'status',
                    ['open' => 'Open', 'closed' => 'Posted', 'confirm' => 'Confirmed', 'verify' => 'Closed'],
                    null,
                    [
                        'class' => 'select-toolbar',
                        'placeholder' => 'All Status',
                    ],
                ) !!}' +
                '</label>' +
                '<label class="ps-2"><a href="#" onclick="applyFilter()"><i class="ph-funnel"></i></a>' +
                '</label>'
            ).css('float', 'left');
            $(".select-toolbar").select2({
                minimumResultsForSearch: Infinity,
                width: '130px'
            });
            dtable.on('select', function(e, dt, type, indexes) {
                var rowArrayDtable = dtable.rows('.selected').data().toArray();
                var rowData = dtable.rows(indexes).data().toArray();
                var id = rowData[0].id;
                $.ajax({
                    type: 'GET',
                    url: getButtonOption,
                    data: {
                        id: id,
                        buttons: buttons,
                    },
                    success: function(response) {
                        if (response.status) {
                            $(".menuoption").html(response.view);
                        }
                    }
                });
                $.ajax({
                    type: 'GET',
                    url: lines,
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $("#dynamic-form-2").html(response.view);
                            $("#dynamic-form-2").show();
                            $("#dynamic-form").hide();
                        }
                    }
                });
            });
            dtable.on('deselect', function(e, dt, type, indexes) {
                if (type === 'row') {
                    deselectRow();
                }
            });

            //submit form create
            $("#dform").on('submit', function(e) {
                $(this).find('#submit_loader').removeAttr('class').addClass('ph-spinner spinner');
            });

            $("body").on("click", ".editBtn", function(e) {
                var url = $(this).attr('href');
                $("#mymodal").find('.modal-body').html(
                    '<center><i class="ph-spinner spinner"></i></center>');
                $("#mymodal").find('.modal-title').html('Edit kasir');
                $("#mymodal").find('.modal-header').removeClass('bg-indigo').addClass('bg-primary');
                $("#mymodal").find('.modal-dialog').removeAttr('class').attr('class',
                    'modal-dialog modal-dialog-scrollable');
                $("#mymodal").modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'JSON',
                    data: {},
                    success: function(response) {
                        if (response.status) {
                            $("#mymodal").find('.modal-body').html(response.view);
                        }
                    }
                });
                e.preventDefault();
            });

            //remove this if you want to update with form submit
            $('body').on('submit', '#formupdate', function(e) {
                swalInit.fire({
                    icon: 'question',
                    title: 'Confirm Save Changes ?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            type: 'PUT',
                            url: $("#formupdate").attr('action'),
                            data: $("#formupdate").serialize(),
                            dataType: "json",
                        }).done(function(data) {
                            return data;
                        }).fail(function(jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == 422) {
                                var xhr = JSON.stringify(JSON.parse(jqXHR.responseText)
                                    .errors);
                            } else {
                                var xhr = JSON.stringify(JSON.parse(jqXHR
                                    .responseText));
                            }
                            swalInit.fire({
                                title: 'Request Error',
                                text: xhr.substring(0, 160),
                                icon: 'error',
                            })
                        })
                    },
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.value != null)
                        if (result.value.status) {
                            swalInit.fire({
                                title: 'Success',
                                text: result.value.msg,
                                icon: 'success',
                                didClose: () => {
                                    dtable.ajax.reload(null, false);
                                    $("#mymodal").modal('hide');
                                }
                            })
                        } else {
                            swalInit.fire({
                                title: 'Error',
                                text: result.value.msg.substring(0, 160),
                                icon: 'error',
                            })
                        }
                })
                e.preventDefault();
            });

            // delete form
            $('body').on('click', '.deleteBtn', function(e) {
                e.preventDefault();
                SwalConfirm('DELETE', $(this).closest('form'));
            });

            //submit form line
            $('body').on("submit", '#l-modal-form', function(e) {
                e.preventDefault();

                let urlForm = $(this).attr('action');
                let dataForm = $(this).serialize();

                CustomAjax(urlForm, 'POST', dataForm, () => {
                    dtable.ajax.reload(null, false);
                    ClearFormLines("#l-modal-form");
                });
            });

            //submit form line
            $('body').on("submit", '#l-upload-form', function(e) {
                e.preventDefault();

                let urlForm = $(this).attr('action');
                let dataForm = $(this).serialize();

                CustomAjax(urlForm, 'POST', dataForm, () => {
                    dtable.ajax.reload(null, false);
                    $("#mymodal").modal('hide');
                });
            });
        });

        const BtnOption = (title, el, e) => {
            e.preventDefault();

            SwalConfirmAjax(title, $(el).data('url'), 'GET', {
                'status': title,
            }, () => {
                dtable.ajax.reload(null, false);
            });
        }


        function changePackage(id, el, modal) {
            $(el).closest("ul").find('.active').removeClass("active").removeClass("fw-semibold").removeClass("text-white")
                .addClass("bg-opacity-10").addClass("text-success");

            $(el).closest("form").find('input[name="package_id"]').val(id)

            $(el).addClass("active").addClass('fw-semibold').addClass('text-white').addClass('text-success').removeClass(
                "bg-opacity-10");
        }


        function showform() {
            dtable.rows().deselect();
            $("#dynamic-form").show();
            $('#dynamic-form').find('select[name="asal_rak_id"]').trigger('change');
        }

        function deselectRow() {
            $("#dynamic-form-2").hide();
            $('.menuoption').html('');
        }

        const RemoveLines = (url, lines_id, e) => {
            CustomAjax(url, 'GET', {}, () => {
                dtable.ajax.reload(null, false);
            });
        }

        const ClearFormLines = (formId) => {
            $(formId).find('select:not([dont-clear])').val('').trigger('change');
            $(formId).find('input:not([disabled])').val('');
        }

        const BtnUploadUrl = (el, e) => {

            e.preventDefault();

            $("#mymodal").find('.modal-body').html('<center><i class="ph-spinner spinner"></i></center>');
            $("#mymodal").find('.modal-title').html('Add URL Google Drive');
            $("#mymodal").find('.modal-header').removeClass('bg-indigo').addClass('bg-success');
            $("#mymodal").find('.modal-dialog').removeAttr('class').attr('class',
                'modal-dialog modal-xl modal-dialog-scrollable');
            $("#mymodal").modal('show');
            // $(".modal-backdrop.show").css('opacity', .05);

            $.ajax({
                url: $(el).data('url'),
                type: 'GET',
                success: function(response) {
                    $("#mymodal").find('.modal-body').html(response);
                }
            });

        }

        $(document).on('init.dt', function(e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            if (api.state.loaded()) {
                var state = api.state.loaded();
                $("select[name='status']").val(state.columns[4].search.search).trigger('change');
                dtable.page(state.start / state.length).draw(false);
            }
        });

        function applyFilter() {
            let status = $(".select-toolbar[name='status']").val();
            dtable.column(4)
                .search(status)
                .draw();
        }
    </script>
@endsection

@section('appmodal')
    <!-- Basic modal -->
    <div id="mymodal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-indigo text-white">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
    <!-- /basic modal -->
@endsection

@section('notification')
    @include('layouts.notification')
@endsection
