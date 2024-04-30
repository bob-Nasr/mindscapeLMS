<?php
/**
 * Template for rendering the user profile editing form.
 *
 * @package    local_guardians
 * @subpackage templates
 * @copyright  Copyright (c) [2024], [Mindscape]
 */

// Ensure this file is included at the top of any PHP file that uses the Moodle API.
require_once(__DIR__ . '/../../../config.php');

// Ensure the user is logged in.
require_login();

// Get the user ID of the current user.
$userid = $USER->id;

// Load the user object.
$user = $DB->get_record('user', array('id' => $userid));

// Load the user's legal guardian information.
$guardian_relationship = $DB->get_record('legalguardians_relationships', array('userid' => $userid));

// Get the legal guardian details if available.
if ($guardian_relationship) {
    $guardian = $DB->get_record('legalguardians', array('guardianid' => $guardian_relationship->guardianid));
}

// Start output buffering.
ob_start();
?>

<div class="form-group">
    <label for="namelegalguardians">Legal Guardian Name:</label>
    <input type="text" id="namelegalguardians" name="namelegalguardians" class="form-control" value="<?php echo isset($guardian) ? $guardian->guardian_name : ''; ?>">
</div>
<div class="form-group">
    <label for="phonenumber">Phone Number:</label>
    <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="<?php echo isset($guardian) ? $guardian->guardian_number : ''; ?>">
</div>
<div class="form-group">
    <label for="relationship">Relationship:</label>
    <select id="relationship" name="relationship" class="form-control">
        <option value="parent" <?php echo (isset($guardian_relationship) && $guardian_relationship->relationship == 'parent') ? 'selected' : ''; ?>>Parent</option>
        <option value="guardian" <?php echo (isset($guardian_relationship) && $guardian_relationship->relationship == 'guardian') ? 'selected' : ''; ?>>Guardian</option>
        <option value="other" <?php echo (isset($guardian_relationship) && $guardian_relationship->relationship == 'other') ? 'selected' : ''; ?>>Other</option>
    </select>
</div>

<?php
// Get the output buffer contents and store them in a variable.
$content = ob_get_clean();

// Print the output.
echo $content;
