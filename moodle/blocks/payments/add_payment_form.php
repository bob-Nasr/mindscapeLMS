<?php
global $DB;

// Fetch distinct student names
$distinctNames = $DB->get_records_sql("
    SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) AS name
    FROM {user} u
    JOIN {role_assignments} ra ON u.id = ra.userid
    JOIN {context} ctx ON ra.contextid = ctx.id
    WHERE ra.roleid = :roleid
    ORDER BY name
", ['roleid' => 5]); // Adjust roleid according to your Moodle configuration

// Fetch student names along with phone numbers
$studentData = $DB->get_records_sql("
    SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS name, u.phone1 AS phone
    FROM {user} u
    JOIN {role_assignments} ra ON u.id = ra.userid
    JOIN {context} ctx ON ra.contextid = ctx.id
    WHERE ra.roleid = :roleid
    ORDER BY name
", ['roleid' => 5]); // Adjust roleid according to your Moodle configuration

// Prepare arrays for JavaScript
$distinctNamesArray = [];
$studentsArray = [];
foreach ($studentData as $student) {
    $studentsArray[] = [
        'id' => $student->id,
        'name' => $student->name,
        'phone' => $student->phone,
    ];
    $distinctNamesArray[] = $student->name;
}

// Convert arrays to JSON for JavaScript usage
$distinctNamesJson = json_encode(array_values(array_unique($distinctNamesArray))); // Ensure distinct names and values array
$studentsJson = json_encode($studentsArray);


// Button to trigger popup form
echo html_writer::start_tag('div', ['class' => 'text-center']);
echo html_writer::link('#', get_string('addpayment', 'block_payments'), [
    'class' => 'btn btn-primary',
    'id' => 'add-payment-button',
    'data-toggle' => 'modal',
    'data-target' => '#popup-modal'
]);
echo html_writer::end_tag('div');

// Popup form (modal)
echo '<div id="popup-modal" class="modal fade" tabindex="-1" role="dialog">';
echo '<div class="modal-dialog" role="document">';
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title">Add Payment</h5>';
echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
echo '<span aria-hidden="true">&times;</span>';
echo '</button>';
echo '</div>';
echo '<div class="modal-body">';

// Payment form
echo '<form id="add-payment-form"; return false;">';

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Student Name', 'studentname');
echo html_writer::select([], 'studentname', '', null, ['class' => 'form-control', 'id' => 'studentname', 'required' => true]);
echo html_writer::end_tag('div');

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Phone Number', 'phonenumber');
echo html_writer::select([], 'phonenumber', '', null, ['class' => 'form-control', 'id' => 'phonenumber', 'required' => true]);
echo html_writer::end_tag('div');

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Amount Paid', 'amount');
echo html_writer::tag('input', '', ['type' => 'number', 'class' => 'form-control', 'id' => 'amount', 'required' => true]);
echo html_writer::end_tag('div');

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Currency', 'currency');
echo html_writer::select(['USD' => 'USD', 'LBP' => 'LBP'], 'currency', 'USD', null, ['class' => 'form-control', 'id' => 'currency']);
echo html_writer::end_tag('div');

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Payment Area', 'paymentArea');
echo html_writer::tag('input', '', ['type' => 'text', 'class' => 'form-control', 'id' => 'paymentArea', 'required' => true]);
echo html_writer::end_tag('div');

echo html_writer::start_tag('div', ['class' => 'form-group']);
echo html_writer::label('Date', 'date');
echo html_writer::tag('input', '', ['type' => 'text', 'class' => 'form-control', 'id' => 'date', 'readonly' => true, 'value' => date('Y-m-d')]);
echo html_writer::end_tag('div');

echo '<button type="submit" class="btn btn-primary">Save Payment</button>';
echo '</form>';

echo '</div>'; // modal-body
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
echo '</div>'; // modal-footer
echo '</div>'; // modal-content
echo '</div>'; // modal-dialog
echo '</div>'; // popup-modal
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentname');
    const phoneSelect = document.getElementById('phonenumber');

    const distinctNames = <?php echo $distinctNamesJson; ?>;
    const studentsData = <?php echo $studentsJson; ?>;

    // Populate the student name dropdown with distinct names
    distinctNames.forEach(name => {
        const option = document.createElement('option');
        option.value = name;
        option.text = name;
        studentSelect.appendChild(option);
    });

    // Populate phone numbers based on selected student
    studentSelect.addEventListener('change', function() {
        const selectedStudentName = studentSelect.value;
        console.log(selectedStudentName + " Changed!!");

        // Find phone numbers for the selected student name
        const selectedPhoneNumbers = studentsData.filter(student => student.name === selectedStudentName).map(student => student.phone);

        // Clear current phone options
        phoneSelect.innerHTML = '';
        
        // Add the selected phone number options
        selectedPhoneNumbers.forEach(phone => {
            const option = document.createElement('option');
            option.value = phone;
            option.text = phone;
            phoneSelect.appendChild(option);
        });
    });

    // Handle form submission
    document.getElementById('add-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const studentname = studentSelect.value;
        const phonenumber = phoneSelect.value;
        const amount = document.getElementById('amount').value;
        const currency = document.getElementById('currency').value;
        const paymentarea = document.getElementById('paymentArea').value;
        const date = document.getElementById('date').value;

        // Make an AJAX POST request to save_payment.php
        fetch('/m433/mindscapeLMS/moodle/blocks/payments/save_payment.php', {  // Ensure this path is correct
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                studentname: studentname,
                phonenumber: phonenumber,
                amount: amount,
                currency: currency,
                paymentarea: paymentarea,
                date: date
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                // Reset the form
                document.getElementById('add-payment-form').reset();
                // Optionally reload the page to update other content if needed
                location.reload();
            } else {
                alert('Error 1: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error 2:', error);
        });
    });
});

</script>
