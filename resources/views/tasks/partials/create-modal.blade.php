<!-- Create Task Modal -->
<div id="createTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeCreateModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Buat Task Baru
                        </h3>
                        <div class="mt-4">
                            <form id="createTaskForm">
                                @csrf
                                <!-- Title -->
                                <div class="mb-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Task <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="title" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Contoh: Perbaiki bug login">
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <!-- Status -->
                                    <div>
                                        <label for="status_id" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select name="status_id" id="status_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                            @foreach($statuses as $status)
                                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Priority -->
                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-gray-700">Prioritas</label>
                                        <select name="priority" id="priority" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                            <option value="low">Rendah</option>
                                            <option value="medium" selected>Sedang</option>
                                            <option value="high">Tinggi</option>
                                        </select>
                                    </div>
                                </div>

                                @can('tasks.assign')
                                <!-- Assignees -->
                                <div class="mb-4">
                                    <label for="assignees" class="block text-sm font-medium text-gray-700">Assign ke</label>
                                    <select name="assignees[]" id="assignees" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                        @foreach($assignableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->roles->first()->name ?? 'User' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcan

                                @can('task-labels.manage')
                                <!-- Labels -->
                                <div class="mb-4">
                                    <label for="labels" class="block text-sm font-medium text-gray-700">Label</label>
                                    <select name="labels[]" id="labels" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                        @foreach($labels as $label)
                                            <option value="{{ $label->id }}">{{ $label->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcan

                                <!-- Deadline -->
                                <div class="mb-4">
                                    <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                                    <input type="datetime-local" name="deadline" id="deadline" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitCreateTask()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Simpan Task
                </button>
                <button type="button" onclick="closeCreateModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let createAssigneesChoice, createLabelsChoice;

    function openCreateModal(statusId = null) {
        document.getElementById('createTaskModal').classList.remove('hidden');
        document.getElementById('createTaskForm').reset();
        
        if(statusId) {
            document.getElementById('status_id').value = statusId;
        }

        // Initialize Choices.js if not already
        if (!createAssigneesChoice) {
            createAssigneesChoice = new Choices('#assignees', { removeItemButton: true, placeholderValue: 'Pilih Assignee...' });
        }
        if (!createLabelsChoice) {
            createLabelsChoice = new Choices('#labels', { removeItemButton: true, placeholderValue: 'Pilih Label...' });
        }
    }

    function closeCreateModal() {
        document.getElementById('createTaskModal').classList.add('hidden');
    }

    function submitCreateTask() {
        const form = document.getElementById('createTaskForm');
        const formData = new FormData(form);

        fetch('{{ route('tasks.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => {
                    location.reload(); // Simple reload for now
                });
                closeCreateModal();
            } else {
                Swal.fire('Error', data.message || 'Gagal membuat task', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
        });
    }
</script>
