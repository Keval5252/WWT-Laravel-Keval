@extends('layouts.app')
@section('title', 'Categorie')

@section('content')
<div class="container mt-4">
    <h2>Categories</h2>
    <button class="btn btn-primary mb-3" onclick="showCreateModal()">Add Category</button>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="categoryTable"></tbody>
    </table>
</div>

<!-- Create/Edit Modal -->
<div id="categoryModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modalTitle">Add Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="categoryId" name="id">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" id="title" name="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image:</label>
                        <input type="file" id="image" name="image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status:</label><br>
                        <input type="radio" name="status" value="1" checked> Active
                        <input type="radio" name="status" value="0"> Inactive
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        fetchCategories();
    });

    function fetchCategories() {
        $.get("{{ route('categories.fetch') }}", function(data) {
            let rows = '';
            data.categories.forEach(category => {
                rows += `
                <tr>
                    <td>${category.id}</td>
                    <td>${category.title}</td>
                    <td>${category.description}</td>
                    <td>${category.image ? `<img src="/storage/${category.image}" width="50">` : ''}</td>
                    <td>${category.status ? 'Active' : 'Inactive'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editCategory(${category.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">Delete</button>
                    </td>
                </tr>
            `;
            });
            $('#categoryTable').html(rows);
        });
    }

    function showCreateModal() {
        $('#modalTitle').text("Add Category");
        $('#categoryId').val('');
        $('#categoryForm')[0].reset();
        $('#categoryModal').modal('show');
    }

    $('#categoryForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let id = $('#categoryId').val();
        let url = id ? `categories/update/${id}` : 'categories/store';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success(response.success); // Show success message
                $('#categoryModal').modal('hide');
                $('#categoryForm')[0].reset(); // Clear form
                fetchCategories(); // Refresh category list
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Validation error
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]); // Show each error in Toastr
                    });
                } else {
                    toastr.error("Something went wrong. Please try again.");
                }
            }
        });
    });

    function editCategory(id) {
        $.get(`categories/edit/${id}`, function(category) {
            $('#modalTitle').text("Edit Category");
            $('#categoryId').val(category.id);
            $('#title').val(category.title);
            $('#description').val(category.description);
            $('input[name="status"][value="' + category.status + '"]').prop('checked', true);
            $('#categoryModal').modal('show');
        });
    }

    function deleteCategory(id) {
        if (!confirm("Are you sure you want to delete this category?")) return;

        $.ajax({
            url: `categories/destroy/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
            },
            success: function(response) {
                toastr.success(response.success); // Change to toastr.success for proper feedback
                fetchCategories();
            },
            error: function(xhr) {
                toastr.error("Something went wrong. Please try again.");
            }
        });
    }
</script>
@endsection