<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// Fetch all products
$products = [];
// Updated SQL to include the image_url for display
$sql = "SELECT product_id, name, description, price, stock_quantity, image_url FROM products ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Product Management</h1>
        <p class="lead text-muted">Manage the inventory and details of all marketplace products.</p>
    </div>
    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="fas fa-plus-circle me-2"></i>Add New Product
    </button>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-box-open me-2"></i>All Products</h5>
            <div class="w-50">
                <input type="text" id="productSearchInput" class="form-control" placeholder="Search by product name...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="productsTable">
                <thead>
                    <tr>
                        <th style="width: 10%;">Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">No products found. Add one to get started!</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="product-row" data-search-term="<?php echo strtolower(htmlspecialchars($product['name'])); ?>">
                                <td>
                                    <?php
                                        // Correct the image path for web display
                                        $image_path = !empty($product['image_url']) 
                                            ? '../' . str_replace('\\', '/', $product['image_url']) 
                                            : '../assets/images/default_product.png';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td class="align-middle"><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                <td class="align-middle">Rs. <?php echo number_format($product['price'], 2); ?></td>
                                <td class="align-middle text-center"><?php echo $product['stock_quantity']; ?></td>
                                <td class="align-middle text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-btn" 
                                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                                            data-product-id="<?php echo $product['product_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                            data-price="<?php echo $product['price']; ?>"
                                            data-stock="<?php echo $product['stock_quantity']; ?>"
                                            data-image-url="<?php echo htmlspecialchars($product['image_url']); ?>">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </button>
                                    <form action="../php/process_admin_actions.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i>Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Product</h5></div>
      <form action="../php/process_admin_actions.php" method="POST">
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" name="name" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
            <div class="mb-3"><label class="form-label">Image URL</label><input type="text" class="form-control" name="image_url" placeholder="assets\images\product.jpg"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Price (Rs.)</label><input type="number" step="0.01" class="form-control" name="price" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Stock Quantity</label><input type="number" class="form-control" name="stock_quantity" required></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="add_product" class="btn btn-success"><i class="fas fa-save me-2"></i>Save Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Product</h5></div>
      <form action="../php/process_admin_actions.php" method="POST">
        <div class="modal-body">
            <input type="hidden" name="product_id" id="editProductId">
            <div class="mb-3"><label class="form-label">Product Name</label><input type="text" class="form-control" id="editName" name="name" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea></div>
            <div class="mb-3"><label class="form-label">Image URL</label><input type="text" class="form-control" id="editImageUrl" name="image_url" placeholder="assets\images\product.jpg"></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label">Price (Rs.)</label><input type="number" step="0.01" class="form-control" id="editPrice" name="price" required></div>
                <div class="col-md-6 mb-3"><label class="form-label">Stock Quantity</label><input type="number" class="form-control" id="editStock" name="stock_quantity" required></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="edit_product" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pre-fill the edit modal
    const editProductModal = document.getElementById('editProductModal');
    editProductModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const modal = this;
        modal.querySelector('#editProductId').value = button.getAttribute('data-product-id');
        modal.querySelector('#editName').value = button.getAttribute('data-name');
        modal.querySelector('#editDescription').value = button.getAttribute('data-description');
        modal.querySelector('#editPrice').value = button.getAttribute('data-price');
        modal.querySelector('#editStock').value = button.getAttribute('data-stock');
        modal.querySelector('#editImageUrl').value = button.getAttribute('data-image-url');
    });

    // Search functionality
    const searchInput = document.getElementById('productSearchInput');
    const productRows = document.querySelectorAll('#productsTable tbody .product-row');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        productRows.forEach(row => {
            const rowSearchTerm = row.getAttribute('data-search-term');
            row.style.display = rowSearchTerm.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>

