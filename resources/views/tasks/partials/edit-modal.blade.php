<!-- Edit Task Model -->
<div id="editTaskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_task_id">
                    
                    <div class="mb-4">
                        <label for="edit_title" class="block text-sm font-medium text-gray-700">Judul Task</label>
                        <input type="text" name="title" id="edit_title" required class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <div class="mb-4">
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="edit_description" rows="3" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="edit_status_id" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status_id" id="edit_status_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="edit_priority" class="block text-sm font-medium text-gray-700">Prioritas</label>
                            <select name="priority" id="edit_priority" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="low">Rendah</option>
                                <option value="medium">Sedang</option>
                                <option value="high">Tinggi</option>
                            </select>
                        </div>
                    </div>

                    @can('tasks.assign')
                    <div class="mb-4">
                        <label for="edit_assignees" class="block text-sm font-medium text-gray-700">Assign ke</label>
                        <select name="assignees[]" id="edit_assignees" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endcan

                    @can('task-labels.manage')
                    <div class="mb-4">
                        <label for="edit_labels" class="block text-sm font-medium text-gray-700">Label</label>
                        <select name="labels[]" id="edit_labels" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @foreach($labels as $label)
                                <option value="{{ $label->id }}">{{ $label->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endcan

                    <div class="mb-4">
                        <label for="edit_deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                        <input type="datetime-local" name="deadline" id="edit_deadline" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse justify-between">
                <div class="flex sm:flex-row-reverse w-full">
                    <button type="button" id="btnEditTask" onclick="submitEditTask()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Update Task
                    </button>
                    <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
                @can('tasks.delete')
                <button type="button" onclick="deleteTask()" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-100 text-base font-medium text-red-700 hover:bg-red-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Hapus
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

<script>
    let editAssigneesChoice, editLabelsChoice;

    function openEditModal(taskId) {
        document.getElementById('editTaskModal').classList.remove('hidden');
        document.getElementById('edit_task_id').value = taskId;

        // Init Choices
        if (!editAssigneesChoice) {
            editAssigneesChoice = new Choices('#edit_assignees', { removeItemButton: true, placeholderValue: 'Pilih Assignee...' });
        }
        if (!editLabelsChoice) {
            editLabelsChoice = new Choices('#edit_labels', { removeItemButton: true, placeholderValue: 'Pilih Label...' });
        }

        // Fetch task details
        fetch(`/tasks/${taskId}`)
            .then(res => res.json())
            .then(task => {
                document.getElementById('edit_title').value = task.title;
                document.getElementById('edit_description').value = task.description || '';
                document.getElementById('edit_status_id').value = task.status_id;
                document.getElementById('edit_priority').value = task.priority;
                document.getElementById('edit_deadline').value = task.deadline ? task.deadline.slice(0, 16) : '';

                // Set Choices values
                const assigneeIds = task.assignees.map(u => u.id.toString());
                editAssigneesChoice.setChoiceByValue(assigneeIds);
                
                const labelIds = task.labels.map(l => l.id.toString());
                editLabelsChoice.setChoiceByValue(labelIds);
            });
    }

    function closeEditModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
        // Need to clear choices? Choices.js handles it mostly, but good to reset if needed
    }

    function submitEditTask() {
        const taskId = document.getElementById('edit_task_id').value;
        const form = document.getElementById('editTaskForm');
        const formData = new FormData(form);
        const btn = document.getElementById('btnEditTask');

        // Show Loading
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i>Menyimpan...';

        fetch(`/tasks/${taskId}`, {
            method: 'POST', // Using POST with _method=PUT
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
                    location.reload();
                });
                closeEditModal();
            } else {
                Swal.fire('Error', data.message, 'error');
                // Reset Button
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Reset Button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function deleteTask() {
        const taskId = document.getElementById('edit_task_id').value;
        Swal.fire({
            title: 'Hapus Task?',
            text: "Data tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        Swal.fire('Error', 'Gagal menghapus task', 'error');
                    }
                });
            }
        });
    }
</script>
