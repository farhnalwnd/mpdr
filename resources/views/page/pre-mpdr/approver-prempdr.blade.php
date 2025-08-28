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
        function addItem(status){
            var selectDiv = document.getElementById('sortable');
            var selectElement = document.getElementById('approver-list');
            var selectedIndex = selectElement.selectedIndex;
            var selectedValue = selectElement.value;
            var selectedNik = selectElement[selectedIndex].getAttribute('data-nik');

            if(selectedValue<1){
                return;
            }

            // Use Laravolt avatar as default, check for user avatar later
            var defaultAvatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedValue)}&color=7F9CF5&background=EBF4FF`;
            var userAvatarUrl = `{{ asset('storage/uploads/user_avatar') }}/${selectedNik}.jpg`;

            var newItem = `
                <li data-nik="${selectedNik}" data-name="${selectedValue}" class="row bg-primary-subtle sortable-item rounded-2">
                    <div class="col-sm-9 d-flex align-items-center">
                        <img src="${defaultAvatarUrl}" class="rounded-circle me-2 user-avatar" width="35" height="35" alt="modernize-img" data-user-avatar="${userAvatarUrl}">
                        <h6 class="mb-0">${selectedValue}</h6>
                    </div>
                    <div class="col-sm-3">
                        <div class="d-flex align-items-center justify-content-end ms-auto">
                            <select class="form-select fw-semibold approver-status ${status == 'Vacant' ? 'text-danger' : 'text-success'}" onChange="changeSelectColor(this)">
                                <option value="Active" class="text-success" ${status == 'Active' ? 'Selected' : ''}>Active</option>
                                <option value="Vacant" class="text-danger" ${status == 'Vacant' ? 'Selected' : ''}>Vacant</option>
                            </select>
                            <button aria-label="Remove Approver" class="btn btn-outline-warning" onClick="deleteItem(this, ${selectedIndex})">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                </li>
            `;

            $('#sortable').append(newItem);

            // Check if user avatar exists and load it if available
            checkAndLoadUserAvatar(selectedNik, $('#sortable li:last-child .user-avatar'));

            if (selectedIndex !== -1) {
                // Menyembunyikan opsi yang dipilih
                selectElement[selectedIndex].classList.add('d-none');
            }
            selectElement.selectedIndex = 0;
        }

        function checkAndLoadUserAvatar(nik, imgElement) {
            // Create a temporary image to test if user avatar exists
            var tempImg = new Image();
            tempImg.onload = function() {
                // If image loads successfully, update the avatar
                imgElement.attr('src', this.src);
            };
            tempImg.onerror = function() {
                // If image fails to load, keep the default Laravolt avatar
                // Do nothing, already using default avatar
            };
            // Set the source to trigger the load/error events
            tempImg.src = `{{ asset('storage/uploads/user_avatar') }}/${nik}.jpg`;
        }

        function deleteItem(button, index){
            var approverName = $(button).closest('li').data('name');

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
                    var selectElement = document.getElementById('approver-list');
                    selectElement[index].classList.remove('d-none');
                    button.parentNode.parentNode.parentNode.remove();

                    Swal.fire({
                        title: 'Removed!',
                        text: `"${approverName}" has been removed from the list.`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        window.addEventListener('load', function () {
            var el = document.getElementById('sortable');
            var sortable = new Sortable(el, {
                animation: 150,
                onEnd(evt) {
                    let order = [];
                    // Ambil urutan baru item setelah di drag-and-drop
                    el.querySelectorAll('.sortable-item').forEach((item, index) => {
                        order.push(item.dataset.id);
                    });
                }
            });
            // mengambil list approver yang sudah dibuat
            var approvers = [];
            $.ajax({
                url: '{{ route('prempdr.selected.approver.list') }}',
                method: 'GET',
                success: function(response) {
                    console.log('data load approver',response);
                    approvers = response; //menyimpan list approver
                },
                error: function() {
                    // Jika gagal, tampilkan pesan error
                    console.log('Error ketika mengambil approver yang sudah dibuat.');
                    // $('#formData').html('<p>There was an error fetching the data.</p>');
                }
            });
            // mengambil list approver
            $.ajax({
                url: '{{ route('prempdr.approver.list.data') }}',
                method: 'GET',
                success: function(response) {
                    response.forEach((item, itemIndex) => {
                        if(approvers && approvers.some(approver => approver.approver_nik === item.nik)){ // mencari jika approver ada dengan yang ada di list approver
                            var approver = approvers.find(approver => approver.approver_nik === item.nik);
                            approver.order = itemIndex;
                        }
                        $('#approver-list').append($('<option>', {
                            value: item.name,
                            'data-nik' : item.nik,
                            text: item.name
                        }));

                    });


                    var selectElement = document.getElementById('approver-list');
                    approvers.forEach((approver, index) => {
                        selectElement.selectedIndex = approver.order+1;

                        var selectedIndex = selectElement.selectedIndex;
                        var selectedValue = selectElement.value;
                        addItem(approver.approver_status);
                    });

                    // Check and load user avatars for existing approvers
                    setTimeout(() => {
                        $('#sortable .user-avatar').each(function() {
                            var imgElement = $(this);
                            var nik = imgElement.closest('li').data('nik');
                            if (nik) {
                                checkAndLoadUserAvatar(nik, imgElement);
                            }
                        });
                    }, 100);
                },
                error: function() {
                    // Jika gagal, tampilkan pesan error
                    console.log('Error ketika mengambil approver list');
                    // $('#formData').html('<p>There was an error fetching the data.</p>');
                }
            });



        });

        const saveButton = document.getElementById('saveList');
        saveButton.addEventListener('click', () => {
            // Simpan urutan approver saat tombol Save diklik
            var orderedNik = [];
            var orderedNames = [];
            var orderedStatuses = [];
            $("#sortable li").each(function() {
                orderedNik.push($(this).data('nik'));
                orderedNames.push($(this).data('name'));
                orderedStatuses.push($(this).find('.approver-status').val());
            });
            $.ajax({
                url: '{{ route('prempdr.approver.update.order') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nik: orderedNik,
                    name: orderedNames,
                    status: orderedStatuses
                },
                success: function(response) {
                    console.log(response);
                    if(response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
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


        function changeSelectColor(select) {
            var selectedValue = select.value; // Mendapatkan nilai yang dipilih
            // Menambahkan class sesuai dengan nilai yang dipilih
            if (selectedValue === 'Active') {
                select.classList.add('text-success');
                select.classList.remove('text-danger');
            } else if (selectedValue === 'Vacant') {
                select.classList.add('text-danger');
                select.classList.remove('text-success');
            }
        }
    </script>
    @endpush

</x-app-layout>
