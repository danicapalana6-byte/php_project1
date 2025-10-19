<?php 
include "db.php"; ?>

<div id="inventory-section" class="container py-4">
    <div class="inventoryMenu-wrapper">
        <h1 class="text-center">Tindahan ni Lola - Inventory</h1>
        <p class="text-center">Last Updated: <span id="last-updated"></span></p>

        <div class="add_products-wrapper mt-4">
            <div class="card p-3 mb-4 shadow-sm">
                <h4>Add New Product</h4>
                <div id="msg" class="mb-2"></div>
                <form id="addProductForm" method="POST">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="text" name="code" class="form-control" placeholder="Code" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="item_name" class="form-control" placeholder="Item Name" required>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_acquired" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="total_stock" class="form-control" placeholder="Total Stock" required>
                        </div>
                        <div class="col-md-2">
                            <select name="category" id="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                <option>Food</option>
                                <option>Beverages</option>
                                <option>Condiments</option>
                                <option>Snacks</option>
                                <option>Canned Goods</option>
                                <option>Toiletries</option>
                                <option>Household Items</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="col-md-2" id="other-category">
                            <input type="text" id="custom-category" class="form-control" placeholder="Enter category">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card p-3 shadow-sm">
                <h4>Inventory List</h4>
                <table id="inventory-table" class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Date Acquired</th>
                            <th>Total Stock</th>
                            <th>Remaining Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-body">
                        <?php
                        $result = $conn->query("SELECT * FROM inventory ORDER BY id ASC");
                        if ($result && $result->num_rows > 0):
                            while ($row = $result->fetch_assoc()):
                        ?>
                            <tr data-id="<?= $row['id'] ?>">
                                <td><?= htmlspecialchars($row['code']) ?></td>
                                <td><?= htmlspecialchars($row['item_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['date_acquired']) ?></td>
                                <td><?= (int)$row['total_stock'] ?></td>
                                <td><?= (int)$row['remaining_stock'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit">✏️ Edit</button>
                                    <button class="btn btn-danger btn-sm btn-delete">🗑️ Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center text-muted">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-2">
                <label>Code</label>
                <input type="text" class="form-control" name="code" id="edit-code" required>
            </div>
            <div class="mb-2">
                <label>Item Name</label>
                <input type="text" class="form-control" name="item_name" id="edit-name" required>
            </div>
            <div class="mb-2">
                <label>Category</label>
                <input type="text" class="form-control" name="category" id="edit-category" required>
            </div>
            <div class="mb-2">
                <label>Date Acquired</label>
                <input type="date" class="form-control" name="date_acquired" id="edit-date" required>
            </div>
            <div class="mb-2">
                <label>Total Stock</label>
                <input type="number" class="form-control" name="total_stock" id="edit-stock" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Update current date
    document.getElementById("last-updated").textContent =
        new Date().toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: '2-digit'});

    // Show input field when "Others" is selected
    $('#category').on('change', function() {
        if ($(this).val() === 'Others') {
            $('#other-category').show();
            $('#custom-category').prop('required', true);
        } else {
            $('#other-category').hide();
            $('#custom-category').prop('required', false);
        }
    });

    // ADD PRODUCT - AJAX no reload
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        const msg = $('#msg');
        msg.text('Adding...').css('color', 'gray');

        // Replace category with custom if Others is selected
        let category = $('#category').val();
        if (category === 'Others') {
            category = $('#custom-category').val();
        }

        const formData = {
            action: 'add',
            code: $('input[name="code"]').val(),
            item_name: $('input[name="item_name"]').val(),
            date_acquired: $('input[name="date_acquired"]').val(),
            total_stock: $('input[name="total_stock"]').val(),
            category: category
        };

        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            success: function(response) {
                msg.text('Product added successfully!').css('color', 'green');
                $('#addProductForm')[0].reset();
                $('#other-category').hide();

                // Append new row to table
                $('#inventory-body').append(`
                    <tr>
                        <td>${formData.code}</td>
                        <td>${formData.item_name}</td>
                        <td>${formData.category}</td>
                        <td>${formData.date_acquired}</td>
                        <td>${formData.total_stock}</td>
                        <td>${formData.total_stock}</td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit">✏️ Edit</button>
                            <button class="btn btn-danger btn-sm btn-delete">🗑️ Delete</button>
                        </td>
                    </tr>
                `);
                setTimeout(() => msg.text(''), 2000);
            },
            error: function(xhr, status, error) {
                msg.text('Error adding product.').css('color', 'red');
                console.error(xhr.responseText);
            }
        });
    });

    // DELETE PRODUCT (AJAX)
    $(document).on('click', '.btn-delete', function() {
        if (!confirm('Delete this item?')) return;
        const id = $(this).closest('tr').data('id');
        const row = $(this).closest('tr');
        $.ajax({
            url: '',
            type: 'POST',
            data: { action: 'delete', id },
            success: function() {
                row.remove();
            },
            error: function() {
                alert('Error deleting product.');
            }
        });
    });

    // EDIT PRODUCT - Open modal
    $(document).on('click', '.btn-edit', function() {
        const row = $(this).closest('tr');
        $('#edit-id').val(row.data('id'));
        $('#edit-code').val(row.find('td:eq(0)').text());
        $('#edit-name').val(row.find('td:eq(1)').text());
        $('#edit-category').val(row.find('td:eq(2)').text());
        $('#edit-date').val(row.find('td:eq(3)').text());
        $('#edit-stock').val(row.find('td:eq(4)').text());
        new bootstrap.Modal('#editModal').show();
    });

    // EDIT PRODUCT - Save changes (AJAX)
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit-id').val();
        const updated = {
            action: 'edit',
            id,
            code: $('#edit-code').val(),
            item_name: $('#edit-name').val(),
            category: $('#edit-category').val(),
            date_acquired: $('#edit-date').val(),
            total_stock: $('#edit-stock').val()
        };

        $.ajax({
            url: '',
            type: 'POST',
            data: updated,
            success: function() {
                const row = $(`tr[data-id="${id}"]`);
                row.find('td:eq(0)').text(updated.code);
                row.find('td:eq(1)').text(updated.item_name);
                row.find('td:eq(2)').text(updated.category);
                row.find('td:eq(3)').text(updated.date_acquired);
                row.find('td:eq(4)').text(updated.total_stock);
                row.find('td:eq(5)').text(updated.total_stock);
                bootstrap.Modal.getInstance($('#editModal')).hide();
            },
            error: function() {
                alert('Error updating product.');
            }
        });
    });
</script>

<?php
// ✅ Handle actions in same file (AJAX only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $code = $_POST['code'];
        $item = $_POST['item_name'];
        $category = $_POST['category'];
        $date = $_POST['date_acquired'];
        $stock = (int)$_POST['total_stock'];

        $stmt = $conn->prepare("INSERT INTO inventory (code, item_name, category, date_acquired, total_stock, remaining_stock)
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $code, $item, $category, $date, $stock, $stock);
        $stmt->execute();
        $stmt->close();
        exit;
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        exit;
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $code = $_POST['code'];
        $item = $_POST['item_name'];
        $category = $_POST['category'];
        $date = $_POST['date_acquired'];
        $stock = (int)$_POST['total_stock'];

        $stmt = $conn->prepare("UPDATE inventory SET code=?, item_name=?, category=?, date_acquired=?, total_stock=?, remaining_stock=? WHERE id=?");
        $stmt->bind_param("ssssiii", $code, $item, $category, $date, $stock, $stock, $id);
        $stmt->execute();
        $stmt->close();
        exit;
    }
}
?>
</body>
</html>
