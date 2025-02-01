@extends('layouts.app')
@section('title', 'Categorie')

@section('content')
<div class="container">
    <button class="btn btn-primary my-3" onclick="showCreateModal()">Add Product</button>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Categories</th>
                <th>Description</th>
                <th>Image</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="productTable"></tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="productModal">
    <div class="modal-dialog">
        <form id="productForm">
            @csrf
            <input type="hidden" id="productId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label>Title:</label>
                    <input type="text" id="title" name="title" class="form-control">

                    <label>Categories:</label>
                    <select id="categories" name="categories[]" class="form-control" multiple>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>

                    <label>Description:</label>
                    <textarea id="description" name="description" class="form-control"></textarea>

                    <label>Image:</label>
                    <input type="file" id="image" name="image" class="form-control">

                    <label>Price:</label>
                    <input type="number" id="price" name="price" step="0.01" class="form-control">

                    <label>Status:</label><br>
                    <input type="radio" name="status" value="1" checked> Active
                    <input type="radio" name="status" value="0"> Inactive
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        fetchProducts();
    });

    // Fetch all products
    function fetchProducts() {
        $.get("{{ route('products.fetch') }}", function(data) {
            let rows = '';
            data.products.forEach(product => {
                rows += `<tr>
                <td>${product.title}</td>
                <td>${product.categories.map(c => c.title).join(', ')}</td>
                <td>${product.description}</td>
                <td>${product.image ? `<img src="/storage/${product.image}" width="50">` : ''}</td>
                <td>${product.price}</td>
                <td>${product.status ? 'Active' : 'Inactive'}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editProduct(${product.id})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">Delete</button>
                </td>
            </tr>`;
            });
            $('#productTable').html(rows);
        });
    }

    // Show modal for adding a product
    function showCreateModal() {
        $('#modalTitle').text("Add Product");
        $('#productId').val('');
        $('#productForm')[0].reset();
        $('#categories').val([]).trigger('change');
        $('#productModal').modal('show');
    }

    // Store or Update Product
    $('#productForm').submit(function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let id = $('#productId').val();
        let url = id ? `products/update/${id}` : 'products/store';

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
                toastr.success(response.success);
                $('#productModal').modal('hide');
                $('#productForm')[0].reset();
                fetchProducts();
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Validation error
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error("Something went wrong. Please try again.");
                }
            }
        });
    });

    // Edit Product
    function editProduct(id) {
        $.get(`products/edit/${id}`, function(product) {
            $('#modalTitle').text("Edit Product");
            $('#productId').val(product.id);
            $('#title').val(product.title);
            $('#description').val(product.description);
            $('#price').val(product.price);
            $('#categories').val(product.categories.map(c => c.id)).trigger('change');
            $('input[name="status"][value="' + product.status + '"]').prop('checked', true);
            $('#productModal').modal('show');
        });
    }

    // Delete Product
    function deleteProduct(id) {
        if (!confirm("Are you sure you want to delete this product?")) return;

        $.ajax({
            url: `products/destroy/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success(response.success);
                fetchProducts();
            },
            error: function() {
                toastr.error("Something went wrong. Please try again.");
            }
        });
    }
</script>
@endsection