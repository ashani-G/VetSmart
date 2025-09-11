<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// --- Optimized Data Fetching ---

// 1. Fetch all clients
$clients = [];
$client_sql = "SELECT user_id, full_name, email, phone_number, address, created_at FROM users WHERE role = 'client' ORDER BY full_name ASC";
$client_result = mysqli_query($conn, $client_sql);
if ($client_result) {
    while ($row = mysqli_fetch_assoc($client_result)) {
        $clients[$row['user_id']] = $row; // Use user_id as key for easy lookup
    }
}

// 2. Fetch all pets and group them by owner
$pets_by_owner = [];
$pet_sql = "SELECT owner_id, pet_id, pet_name, species, breed, date_of_birth, gender FROM pets ORDER BY pet_name ASC";
$pet_result = mysqli_query($conn, $pet_sql);
if ($pet_result) {
    while ($row = mysqli_fetch_assoc($pet_result)) {
        $pets_by_owner[$row['owner_id']][] = $row;
    }
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Client & Pet Management</h1>
        <p class="lead text-muted">A complete directory of all registered clients and their pets.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-users me-2"></i>Client Directory</h5>
            <div class="w-50 d-flex">
                 <input type="text" id="clientSearchInput" class="form-control me-2" placeholder="Search by name, email, or phone...">
                 <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal"><i class="fas fa-plus me-2"></i>Add Client</button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="clientsTable">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Contact Info</th>
                        <th>Member Since</th>
                        <th class="text-center">Pets</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">No clients found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): 
                            $pet_count = isset($pets_by_owner[$client['user_id']]) ? count($pets_by_owner[$client['user_id']]) : 0;
                        ?>
                            <tr class="client-row" data-search-term="<?php echo strtolower(htmlspecialchars($client['full_name'] . ' ' . $client['email'] . ' ' . $client['phone_number'])); ?>">
                                <td><strong><?php echo htmlspecialchars($client['full_name']); ?></strong></td>
                                <td>
                                    <div class="small text-muted"><?php echo htmlspecialchars($client['email']); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($client['phone_number'] ?: 'N/A'); ?></div>
                                </td>
                                <td><?php echo date('F j, Y', strtotime($client['created_at'])); ?></td>
                                <td class="text-center"><span class="badge bg-primary rounded-pill"><?php echo $pet_count; ?></span></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewPetsModal" data-client-id="<?php echo $client['user_id']; ?>" data-client-name="<?php echo htmlspecialchars($client['full_name']); ?>">
                                        <i class="fas fa-paw"></i> View Pets
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Pets Modal -->
<div class="modal fade" id="viewPetsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewPetsModalLabel">Registered Pets</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Showing pets for <strong id="clientNameForPets"></strong>.</p>
        <div id="petsContainer" class="table-responsive">
            <!-- Pets table will be injected here by JavaScript -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="../php/process_admin_actions.php" method="POST">
                    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Phone Number</label><input type="tel" name="phone_number" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_client" class="btn btn-primary">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Convert PHP array to JSON for use in JavaScript
$pets_json = json_encode($pets_by_owner);
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const petsData = <?php echo $pets_json; ?>;
    
    // View Pets Modal Logic
    const viewPetsModal = document.getElementById('viewPetsModal');
    viewPetsModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const clientId = button.getAttribute('data-client-id');
        const clientName = button.getAttribute('data-client-name');
        
        document.getElementById('clientNameForPets').textContent = clientName;
        const petsContainer = document.getElementById('petsContainer');
        const clientPets = petsData[clientId] || [];
        
        if (clientPets.length > 0) {
            let tableHtml = '<table class="table table-striped"><thead><tr><th>Name</th><th>Species</th><th>Breed</th><th>Gender</th></tr></thead><tbody>';
            clientPets.forEach(pet => {
                tableHtml += `<tr>
                    <td>${pet.pet_name}</td>
                    <td>${pet.species}</td>
                    <td>${pet.breed}</td>
                    <td>${pet.gender}</td>
                </tr>`;
            });
            tableHtml += '</tbody></table>';
            petsContainer.innerHTML = tableHtml;
        } else {
            petsContainer.innerHTML = '<p class="text-center text-muted p-4">This client has no registered pets.</p>';
        }
    });

    // Client Search Logic
    const searchInput = document.getElementById('clientSearchInput');
    const clientRows = document.querySelectorAll('#clientsTable tbody .client-row');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        clientRows.forEach(row => {
            const rowSearchTerm = row.getAttribute('data-search-term');
            row.style.display = rowSearchTerm.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
