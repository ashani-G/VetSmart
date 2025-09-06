<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch all pets belonging to the currently logged-in user, including the image URL
$user_id = $_SESSION['user_id'];
$pets = []; // Initialize an empty array to store pets

$sql = "SELECT pet_id, pet_name, species, breed, date_of_birth, gender, medical_history, pet_image_url FROM pets WHERE owner_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pets[] = $row;
    }
}
mysqli_stmt_close($stmt);

// Function to calculate age from date of birth
function calculate_age($dob) {
    if (!$dob) return 'N/A';
    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate);
    return $age->y . " years, " . $age->m . " months";
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">My Pets</h1>
        <p class="lead text-muted">Manage your pet profiles. Add, view, or update their information here.</p>
    </div>
    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addPetModal">
        <i class="fas fa-plus me-2"></i>Add New Pet
    </button>
</div>


<!-- Display Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Pets Grid -->
<div class="row">
    <?php if (empty($pets)): ?>
        <div class="col-12">
            <div class="text-center p-5 border rounded" style="background-color: #f8f9fa;">
                <i class="fas fa-paw fa-3x text-muted mb-3"></i>
                <h4 class="fw-bold">No Pets Yet</h4>
                <p class="text-muted">You haven't added any pets. Click the button above to add one!</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($pets as $pet): ?>
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php if (!empty($pet['pet_image_url']) && file_exists('../' . $pet['pet_image_url'])): ?>
                                    <img src="../<?php echo htmlspecialchars($pet['pet_image_url']); ?>" alt="<?php echo htmlspecialchars($pet['pet_name']); ?>" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-paw fa-2x text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-4">
                                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($pet['pet_name']); ?></h4>
                                <p class="text-muted mb-1"><?php echo htmlspecialchars($pet['species']); ?> - <?php echo htmlspecialchars($pet['breed']); ?></p>
                                <p class="text-muted mb-0"><strong>Age:</strong> <?php echo calculate_age($pet['date_of_birth']); ?></p>
                            </div>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-outline-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editPetModal" 
                                        data-pet-id="<?php echo $pet['pet_id']; ?>" 
                                        data-pet-name="<?php echo htmlspecialchars($pet['pet_name']); ?>"
                                        data-species="<?php echo htmlspecialchars($pet['species']); ?>"
                                        data-breed="<?php echo htmlspecialchars($pet['breed']); ?>"
                                        data-dob="<?php echo htmlspecialchars($pet['date_of_birth']); ?>"
                                        data-gender="<?php echo htmlspecialchars($pet['gender']); ?>"
                                        data-history="<?php echo htmlspecialchars($pet['medical_history']); ?>"
                                        data-image-url="../<?php echo htmlspecialchars($pet['pet_image_url']); ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePetModal" data-pet-id="<?php echo $pet['pet_id']; ?>" data-pet-name="<?php echo htmlspecialchars($pet['pet_name']); ?>">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Add Pet Modal -->
<div class="modal fade" id="addPetModal" tabindex="-1" aria-labelledby="addPetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPetModalLabel"><i class="fas fa-paw me-2"></i>Add a New Pet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="../php/process_pet_actions.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3"><label for="addPetName" class="form-label">Pet Name</label><input type="text" class="form-control" id="addPetName" name="pet_name" required></div>
          <div class="mb-3"><label for="addPetImage" class="form-label">Pet Image</label><input type="file" class="form-control" id="addPetImage" name="pet_image"></div>
          <div class="mb-3"><label for="addSpecies" class="form-label">Species</label><input type="text" class="form-control" id="addSpecies" name="species" placeholder="e.g., Dog, Cat" required></div>
          <div class="mb-3"><label for="addBreed" class="form-label">Breed</label><input type="text" class="form-control" id="addBreed" name="breed"></div>
          <div class="mb-3"><label for="addDob" class="form-label">Date of Birth</label><input type="date" class="form-control" id="addDob" name="date_of_birth"></div>
          <div class="mb-3"><label for="addGender" class="form-label">Gender</label><select class="form-select" id="addGender" name="gender"><option value="Male">Male</option><option value="Female">Female</option><option value="Unknown">Unknown</option></select></div>
          <div class="mb-3"><label for="addMedicalHistory" class="form-label">Medical History (Optional)</label><textarea class="form-control" id="addMedicalHistory" name="medical_history" rows="3"></textarea></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="add_pet" class="btn btn-primary">Save Pet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Pet Modal -->
<div class="modal fade" id="editPetModal" tabindex="-1" aria-labelledby="editPetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPetModalLabel"><i class="fas fa-edit me-2"></i>Edit Pet Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="../php/process_pet_actions.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" id="editPetId" name="pet_id">
          <div class="text-center mb-3">
              <img id="editPetImagePreview" src="" alt="Pet Image" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
          </div>
          <div class="mb-3"><label for="editPetName" class="form-label">Pet Name</label><input type="text" class="form-control" id="editPetName" name="pet_name" required></div>
          <div class="mb-3"><label for="editPetImage" class="form-label">Change Image (Optional)</label><input type="file" class="form-control" id="editPetImage" name="pet_image"></div>
          <div class="mb-3"><label for="editSpecies" class="form-label">Species</label><input type="text" class="form-control" id="editSpecies" name="species" required></div>
          <div class="mb-3"><label for="editBreed" class="form-label">Breed</label><input type="text" class="form-control" id="editBreed" name="breed"></div>
          <div class="mb-3"><label for="editDob" class="form-label">Date of Birth</label><input type="date" class="form-control" id="editDob" name="date_of_birth"></div>
          <div class="mb-3"><label for="editGender" class="form-label">Gender</label><select class="form-select" id="editGender" name="gender"><option value="Male">Male</option><option value="Female">Female</option><option value="Unknown">Unknown</option></select></div>
          <div class="mb-3"><label for="editMedicalHistory" class="form-label">Medical History</label><textarea class="form-control" id="editMedicalHistory" name="medical_history" rows="3"></textarea></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="update_pet" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Pet Modal -->
<div class="modal fade" id="deletePetModal" tabindex="-1" aria-labelledby="deletePetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deletePetModalLabel"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete the profile for <strong id="deletePetName"></strong>? This action cannot be undone.</p>
        <form action="../php/process_pet_actions.php" method="POST">
          <input type="hidden" id="deletePetId" name="pet_id">
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="delete_pet" class="btn btn-danger">Delete Pet</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Script for Edit Pet Modal
    var editPetModal = document.getElementById('editPetModal');
    editPetModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        // Extract info from data-* attributes
        var petId = button.getAttribute('data-pet-id');
        var petName = button.getAttribute('data-pet-name');
        var species = button.getAttribute('data-species');
        var breed = button.getAttribute('data-breed');
        var dob = button.getAttribute('data-dob');
        var gender = button.getAttribute('data-gender');
        var history = button.getAttribute('data-history');
        var imageUrl = button.getAttribute('data-image-url');
        
        // Update the modal's content.
        var modal = this;
        modal.querySelector('#editPetId').value = petId;
        modal.querySelector('#editPetName').value = petName;
        modal.querySelector('#editSpecies').value = species;
        modal.querySelector('#editBreed').value = breed;
        modal.querySelector('#editDob').value = dob;
        modal.querySelector('#editGender').value = gender;
        modal.querySelector('#editMedicalHistory').value = history;
        
        var imagePreview = modal.querySelector('#editPetImagePreview');
        if (imageUrl && !imageUrl.endsWith('null')) {
            imagePreview.src = imageUrl;
        } else {
            imagePreview.src = 'https://placehold.co/100x100/EFEFEF/AAAAAA?text=No+Image';
        }
    });

    // Script for Delete Pet Modal
    var deletePetModal = document.getElementById('deletePetModal');
    deletePetModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var petId = button.getAttribute('data-pet-id');
        var petName = button.getAttribute('data-pet-name');

        var modal = this;
        modal.querySelector('#deletePetId').value = petId;
        modal.querySelector('#deletePetName').textContent = petName;
    });
});
</script>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>

