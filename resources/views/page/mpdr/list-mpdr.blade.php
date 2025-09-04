<x-app-layout>
    @section('title')
        List MPDR
    @endsection

    @push('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.bootstrap.min.css">
    @endpush


    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">List MPDR</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">List MPDR</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="{{ asset('assets') }}/images/breadcrumb/ChatBc.png" alt="modernize-img"
                            class="img-fluid mb-n4">
                    </div>
                </div>
                <div class="col-3">
                    <button onclick="history.back()" class="btn btn-sm btn-primary flex-end">Back</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href=" {{ route('mpdr.create') }} " class="btn btn-outline-primary">Create New Form</a>
    </div>

    <div class="card">
        <div class="card-body">
           <div class="table-responsive">
            <table class="table table-striped table-bordered" id="mpdrTable">
                <thead class="header-item">
                    <th>No</th>
                    <th>Project Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </thead>
                <tbody>
                </tbody>
            </table>
           </div>
        </div>
    </div>

        <div class="modal fade" id="viewFormModal" tabindex="-1" aria-labelledby="viewFormModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewFormModalLabel">Detail Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Konten dari AJAX akan dimuat di dalam div di bawah ini --}}
                        <div id="modalContent">
                            <p class="text-center">Loading...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.js"></script>
    <script>
        $('#mpdrTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route('mpdr.form.list') }}',
                type: 'GET',
                dataSrc: function(response) {
                    return response;
                }
            },
            columns: [
                { data: 'no', name: 'no_reg' },
                { data: 'product_name', name: 'name' },
                { data: 'status', name: 'status',
                    render: function(data, type, row) {
                        if (data === 'Rejected') {
                            return '<span class="text-danger">' + data + '</span>';
                        } else if (data === 'Approved') {
                            return '<span class="text-success">' + data + '</span>';
                        } else if (data === 'In Approval'){
                            return '<span class="text-primary">' + data + '</span>';
                        } if (data === 'Draft'){
                            return '<span class="text-body-secondary">' + data + '</span>';
                        }
                        return data;
                    }
                },
                { data: null, name: 'action', orderable: false, searchable: false, 
                    render: function(data, type, row) {
                        if (data.status === 'Draft') {
                            const editRoute = "{{ route('mpdr.edit', ':formId') }}".replace(':formId', row.no);
                            const deleteRoute = "{{ route('mpdr.destroy', ':formId') }}".replace(':formId', row.no);

                            return `
                            <div class="d-flex gap-6">
                                <a href="${editRoute}" class="btn btn-outline-primary " >Edit Form</a>
                                <form id="delete-form-${row.no}" action="${deleteRoute}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <a href="javascript:void(0)" class="btn btn-outline-danger" data-form-no="${row.no}" onClick="confirmDelete(this)">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                </form>
                            </div>
                            `;
                        } else {
                            const route = "{{ route('mpdr.form', ':formId') }}";
                            const url = route.replace(':formId', row.no);
                            @if(auth()->user()->hasRole('super-admin'))

                            if(data.status === 'In Approval')
                            {
                                const sendEmailUrl = "{{ route('mpdr.send.mail.gm', ':formId') }}".replace(':formId', row.no);
                                return `<div class="d-flex gap-6">
                                            <button type="button" class="btn btn-outline-primary view-form-btn" data-bs-toggle="modal" data-bs-target="#viewFormModal" data-form-id="${row.no}">
                                                View Form
                                            </button>
                                            <form id="send-form-${row.no}" action="${sendEmailUrl}" method="POST">
                                                @csrf
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="hidden" name="_method" value="POST">
                                                <a href="javascript:void(0)" class="btn btn-outline-warning" send-form-no="${row.no}" onClick="confirmEmail(this)">
                                                    Send Email To GM
                                                </a>
                                            </form>
                                        </div>
                                        `;
                            }

                            @endif
                            
                            return `<button type="button" class="btn btn-outline-primary view-form-btn" data-bs-toggle="modal" data-bs-target="#viewFormModal" data-form-id="${row.no}">
                                        View Form
                                    </button>`;
                        }
                    }
                }
            ]
        });

                $('#mpdrTable tbody').on('click', '.view-form-btn', function () {
                        var formId = $(this).data('form-id');
                        var url = "{{ route('mpdr.form.detail', ':formId') }}".replace(':formId', formId);
                        console.log("Fetching data from URL:", url); // Debugging line

                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function (response) {
                                $('#modalContent').html(response);
                            },
                            error: function (xhr) {
                                $('#modalContent').html('<p class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</p>');
                                console.error('AJAX Error:', xhr);
                            }
                        });
                    });
        

        // Function untuk konfirmasi delete user
        function confirmDelete(button){
            var formNo = button.getAttribute('data-form-no');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + formNo).submit();
                }
            });
        }

        // Function untuk konfirmasi send email to gm
        function confirmEmail(button){
            var formNo = button.getAttribute('send-form-no');
            Swal.fire({
                title: 'Are you sure?',
                text: "Email will be send to GM!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send to GM!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('send-form-' + formNo).submit();
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
