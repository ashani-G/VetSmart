<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// Fetch all reviews, ordered by pending first
$reviews = [];
$sql = "SELECT review_id, client_name, rating, comment, is_approved, created_at FROM reviews ORDER BY is_approved ASC, created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Review Management</h1>
        <p class="lead text-muted">Approve or delete client feedback for the public website.</p>
    </div>
</div>

<!-- Display Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-comments me-2"></i>Submitted Reviews</h5>
            <div class="w-50">
                <input type="text" id="reviewSearchInput" class="form-control" placeholder="Search by client name...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reviewsTable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th class="text-center">Rating</th>
                        <th>Comment</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">No reviews have been submitted yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <tr class="review-row" data-search-term="<?php echo strtolower(htmlspecialchars($review['client_name'])); ?>">
                            <td class="align-middle"><strong><?php echo htmlspecialchars($review['client_name']); ?></strong></td>
                            <td class="align-middle text-center text-warning">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="<?php echo $i < $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </td>
                            <td class="align-middle" style="max-width: 400px;"><?php echo htmlspecialchars($review['comment']); ?></td>
                            <td class="align-middle text-center">
                                <?php if ($review['is_approved']): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-end">
                                <form action="../php/process_admin_actions.php" method="POST" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                    <?php if (!$review['is_approved']): ?>
                                        <button type="submit" name="approve_review" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-check-circle me-1"></i>Approve
                                        </button>
                                    <?php endif; ?>
                                    <button type="submit" name="delete_review" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to permanently delete this review?');">
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

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('reviewSearchInput');
    const reviewRows = document.querySelectorAll('#reviewsTable tbody .review-row');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        reviewRows.forEach(row => {
            const rowSearchTerm = row.getAttribute('data-search-term');
            row.style.display = rowSearchTerm.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
