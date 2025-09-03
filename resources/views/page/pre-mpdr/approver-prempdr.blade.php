<x-app-layout>
    @section('title')
        Approver Pre MPDR
    @endsection

    @push('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .sortable-item {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 5px;
            background: #f4f4f4;
            cursor: pointer;
        }
    </style>
    @endpush

    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Approver Pre MPDR</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a class="text-muted text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Approver Pre MPDR</li>
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


    <div class="container">

        <h2>Drag and Drop Approver</h2>

        <div class="mb-3 d-flex ">
            <div class="me-2">
                <select class="form-select fw-semibold" id="approver-list">
                    <option value="" disabled>Select Approver</option>
                </select>
            </div>
            <div class="me-2">
                <select class="form-select fw-semibold" id="approver-level">
                    <option value="" disabled>-- Level --</option>
                </select>
            </div>
            <button class="btn btn-outline-success me-2" onClick="addItem()">
                Add
                <i class="ti ti-plus"></i>
            </button>
            <button id="saveList" class="btn btn-outline-primary ms-auto">
                Save
                <i class="ti ti-folder"></i>
            </button>
        </div>

        <ul id="sortable"></ul>
    </div>

@push('scripts')
    <!-- Sortable.js from CDNJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const DELETE_URL_TEMPLATE = "{{ route('prempdr.approver.destroy', ['nik' => '__NIK__']) }}";
        // Fungsi untuk menambahkan item approver ke list
        function addItem(status) {
            var sortableList = document.getElementById('sortable');
            var approverSelect = document.getElementById('approver-list');
            var levelSelect = document.getElementById('approver-level');

            var selectedApproverIndex = approverSelect.selectedIndex;
            var selectedApproverValue = approverSelect.value;
            var selectedNik = approverSelect.options[selectedApproverIndex].getAttribute('data-nik');

            var selectedLevelIndex = levelSelect.selectedIndex;
            var selectedLevel = levelSelect.value;

            // Validasi: pastikan approver dan level sudah dipilih
            if (selectedApproverValue < 1 || selectedLevel < 1) {
                Swal.fire('Incomplete', 'Please select both an approver and a level.', 'warning');
                return;
            }

            var defaultAvatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedApproverValue)}&color=7F9CF5&background=EBF4FF`;
            var userAvatarUrl = `{{ asset('storage/uploads/user_avatar') }}/${selectedNik}.jpg`;

            var newItem = `
                    <li data-nik="${selectedNik}" data-name="${selectedApproverValue}" data-level="${selectedLevel}" class="row bg-primary-subtle sortable-item rounded-2">
                        <div class="col-sm-6 d-flex align-items-center">
                            <img src="${defaultAvatarUrl}" class="rounded-circle me-2 user-avatar" width="35" height="35" alt="avatar" data-user-avatar="${userAvatarUrl}">
                            <h6 class="mb-0 me-2">${selectedApproverValue}</h6>
                            <span class="badge bg-info">Level ${selectedLevel}</span>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center justify-content-end ms-auto">
                                <select class="form-select fw-semibold approver-status ${status == 'Vacant' ? 'text-danger' : 'text-success'}" onChange="changeSelectColor(this)">
                                    <option value="Active" class="text-success" ${status == 'Active' ? 'Selected' : ''}>Active</option>
                                    <option value="Vacant" class="text-danger" ${status == 'Vacant' ? 'Selected' : ''}>Vacant</option>
                                </select>
                                <button aria-label="Remove Approver" class="btn btn-outline-warning ms-2" onClick="deleteItem(this, ${selectedApproverIndex})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                `;

            $('#sortable').append(newItem);
            checkAndLoadUserAvatar(selectedNik, $('#sortable li:last-child .user-avatar'));

            // Sembunyikan approver dan level yang sudah dipilih dari dropdown
            approverSelect.options[selectedApproverIndex].classList.add('d-none');
            levelSelect.remove(selectedLevelIndex);

            // Reset pilihan dropdown
            approverSelect.selectedIndex = 0;
            levelSelect.selectedIndex = 0;
        }

        // Fungsi untuk menghapus item dari list
        function deleteItem(button, approverIndex) {
            var listItem = $(button).closest('li');
            var nikToDelete = listItem.data('nik');
            var approverName = listItem.data('name');
            var level = listItem.data('level');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to remove "${approverName}" from the approver list?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let finalUrl = DELETE_URL_TEMPLATE.replace('__NIK__', nikToDelete);
                    $.ajax({
                        url: finalUrl, // URL ke route destroy
                        type: 'POST', // Gunakan POST untuk 'menipu' method DELETE
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE' // Ini adalah method spoofing yang dipahami Laravel
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                // 1. Kembalikan approver ke dropdown
                                var approverSelect = document.getElementById('approver-list');
                                approverSelect.options[approverIndex].classList.remove('d-none');

                                // 2. Kembalikan level ke dropdown dan urutkan
                                var levelSelect = document.getElementById('approver-level');
                                $('#approver-level').append($('<option>', { value: level, text: level }));
                                sortSelectOptions(levelSelect);

                                // 3. Hapus item dari tampilan
                                listItem.remove();

                                // 4. Tampilkan notifikasi sukses
                                Swal.fire('Deleted!', `"${approverName}" has been successfully removed.`, 'success');
                            } else {
                                // Tampilkan pesan error dari backend jika ada masalah
                                Swal.fire('Failed!', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            // Tampilkan pesan error jika AJAX gagal (misal: server down)
                            Swal.fire('Error!', 'An error occurred while communicating with the server.', 'error');
                        }
                    });
                    // Kembalikan approver ke dropdown
                    //var approverSelect = document.getElementById('approver-list');
                    //approverSelect.options[approverIndex].classList.remove('d-none');

                    // Kembalikan level ke dropdown dan urutkan
                    //var levelSelect = document.getElementById('approver-level');
                    //$('#approver-level').append($('<option>', { value: level, text: level }));
                    //sortSelectOptions(levelSelect);

                    //listItem.remove();

                    //Swal.fire('Removed!', `"${approverName}" has been removed.`, 'success');
                }
            });
        }

        // Fungsi pembantu untuk mengurutkan opsi pada select
        function sortSelectOptions(selectElement) {
            var options = $(selectElement).find('option');
            options.sort(function (a, b) {
                if (a.value === "") return -1; // Keep placeholder at top
                if (b.value === "") return 1;
                return +a.text - +b.text;
            });
            $(selectElement).html(options);
            selectElement.selectedIndex = 0;
        }


        // Inisialisasi saat halaman dimuat
        window.addEventListener('load', function () {
            // Inisialisasi Sortable.js
            var el = document.getElementById('sortable');
            var sortable = new Sortable(el, { animation: 150 });

            // Mengambil list level yang tersedia dari backend
            $.ajax({
                url: '{{ route('prempdr.approver.available.levels') }}',
                method: 'GET',
                success: function (levels) {
                    var levelSelect = $('#approver-level');
                    levels.forEach(level => {
                        levelSelect.append($('<option>', {
                            value: level,
                            text: level
                        }));
                    });
                },
                error: function () {
                    console.error('Error fetching available levels.');
                }
            });

            // Mengambil data approver (kode yang sudah ada)
            var existingApprovers = []; // Variabel untuk menyimpan data approver dari DB
            $.ajax({
                url: '{{ route('prempdr.selected.approver.list') }}',
                method: 'GET',
                async: false, // Dibuat synchronous agar data ini tersedia untuk ajax berikutnya
                success: function (response) {
                    existingApprovers = response;
                },
                error: function () {
                    console.error('Error fetching selected approvers.');
                }
            });

            // Mengambil semua list user untuk dropdown approver
            $.ajax({
                url: '{{ route('prempdr.approver.list.data') }}',
                method: 'GET',
                success: function (response) {
                    var approverSelect = $('#approver-list');
                    response.forEach(item => {
                        approverSelect.append($('<option>', {
                            value: item.name,
                            'data-nik': item.nik,
                            text: item.name
                        }));
                    });

                    // Setelah semua user dimuat, tampilkan approver yang sudah ada
                    var approverSelectEl = document.getElementById('approver-list');
                    var levelSelectEl = document.getElementById('approver-level');

                    existingApprovers.sort((a, b) => a.order - b.order).forEach(approver => {
                        // Cari option yang sesuai di dropdown
                        for (let i = 0; i < approverSelectEl.options.length; i++) {
                            if (approverSelectEl.options[i].getAttribute('data-nik') === approver.approver_nik) {
                                approverSelectEl.selectedIndex = i;
                                break;
                            }
                        }

                        // Temporarily add the level to the dropdown to select it
                        $('#approver-level').append($('<option>', { value: approver.level, text: approver.level, selected: true }));

                        addItem(approver.approver_status);
                    });
                },
                error: function () {
                    console.error('Error fetching all approver list.');
                }
            });
        });

        // Event listener untuk tombol Save
        const saveButton = document.getElementById('saveList');
        saveButton.addEventListener('click', () => {
            var orderedNik = [];
            var orderedNames = [];
            var orderedStatuses = [];
            var orderedLevels = [];

            $("#sortable li").each(function () {
                orderedNik.push($(this).data('nik'));
                orderedNames.push($(this).data('name'));
                orderedLevels.push($(this).data('level')); // Ambil level dari data-attribute
                orderedStatuses.push($(this).find('.approver-status').val());
            });

            $.ajax({
                url: '{{ route('prempdr.approver.update.order') }}', // Sesuaikan dengan nama route Anda
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nik: orderedNik,
                    name: orderedNames,
                    status: orderedStatuses,
                    level: orderedLevels
                },
                success: function (response) {
                    console.log(response);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('There was an error saving the order: ', error);
                    if (response && response.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.'
                        });
                    }
                }
            });
        });

        // Fungsi lainnya yang sudah ada
        function checkAndLoadUserAvatar(nik, imgElement) {
            var tempImg = new Image();
            tempImg.onload = function () { imgElement.attr('src', this.src); };
            tempImg.src = `{{ asset('storage/uploads/user_avatar') }}/${nik}.jpg`;
        }

        function changeSelectColor(select) {
            select.classList.toggle('text-success', select.value === 'Active');
            select.classList.toggle('text-danger', select.value === 'Vacant');
        }
    </script>
    @endpush

</x-app-layout>
