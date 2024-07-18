<?php
class block_payments extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_payments');
    }

    function get_content() {
        global $DB, $OUTPUT, $USER, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $content = '';

        // Include the popup form
        ob_start();
        require_once(__DIR__ . '/add_payment_form.php');
        $content .= ob_get_clean();

        // Add filter form with dropdowns
        $content .= '<form method="GET" action="">';
        $content .= '<label for="filter_name">Filter by Name:</label>';
        $content .= '<select id="filter_name" name="filter_name">';
        $content .= '<option value="">Select Name</option>';
        // Fetch distinct user names for the filter dropdown
        $name_sql = "SELECT DISTINCT CONCAT(u.firstname, ' ', u.lastname) AS user_name 
                    FROM {payments} p
                    JOIN {user} u ON p.userid = u.id";
        $names = $DB->get_records_sql($name_sql);
        foreach ($names as $name) {
            $selected = (isset($_GET['filter_name']) && $_GET['filter_name'] === $name->user_name) ? 'selected' : '';
            $content .= '<option value="' . htmlspecialchars($name->user_name) . '" ' . $selected . '>' . htmlspecialchars($name->user_name) . '</option>';
        }
        $content .= '</select>';
        $content .= '<label for="filter_paymentarea">Filter by Payment Area:</label>';
        $content .= '<select id="filter_paymentarea" name="filter_paymentarea">';
        $content .= '<option value="">Select Payment Area</option>';
        // Fetch distinct Payment Areas for the filter dropdown
        $paymentarea_sql = "SELECT DISTINCT paymentarea FROM {payments}";
        $paymentareas = $DB->get_records_sql($paymentarea_sql);        
        foreach ($paymentareas as $paymentarea) {
            $selected = (isset($_GET['filter_paymentarea']) && $_GET['filter_paymentarea'] === $paymentarea->paymentarea) ? 'selected' : '';
            $content .= '<option value="' . htmlspecialchars($paymentarea->paymentarea) . '" ' . $selected . '>' . htmlspecialchars($paymentarea->paymentarea) . '</option>';
        }
        $content .= '</select>';
        $content .= '<button type="submit">Filter</button>';
        $content .= '</form>';

        // Fetch recent payments from the database
        try {
            $sql = "SELECT
                        p.id AS payment_id,
                        CONCAT(u.firstname, ' ', u.lastname) AS user_name,
                        u.phone1 AS user_phone,
                        p.amount,
                        p.currency,
                        FROM_UNIXTIME(p.timecreated) AS payment_date,
                        p.paymentarea,
                        p.gateway
                    FROM
                        {payments} p
                    JOIN
                        {user} u ON p.userid = u.id
                    WHERE 1=1";
                                
            // Initialize parameters array
            $params = array();

            // Add filters based on provided input
            if (!empty($_GET['filter_name'])) {
                $filter_name = '%' . $DB->sql_like_escape($_GET['filter_name']) . '%';
                $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
                $params[] = $filter_name;
            }
            if (!empty($_GET['filter_paymentarea'])) {
                $filter_paymentarea = $DB->sql_like_escape($_GET['filter_paymentarea']);
                $sql .= " AND p.paymentarea = ?";
                $params[] = $filter_paymentarea;
            }

            // Finalize SQL query with ordering
            $sql .= " ORDER BY p.timecreated DESC";

            try {
                // Execute the query with parameters
                $payments = $DB->get_records_sql($sql, $params);
            } catch (Exception $e) {
                $content .= 'Error fetching payments: ' . $e->getMessage();
                return $this->content;
            }

            // Build HTML table
            $content .= '<table border="1" cellpadding="10" cellspacing="0">';
            $content .= '<thead>';
            $content .= '<tr>';
            $content .= '<th>User Name</th>';
            $content .= '<th>Phone Number</th>'; // Added column for Guardian Name
            $content .= '<th>Amount</th>';
            $content .= '<th>Currency</th>';
            $content .= '<th>Gateway</th>';
            $content .= '<th>Date</th>';
            $content .= '<th>Payment Area</th>';
            $content .= '</tr>';
            $content .= '</thead>';
            $content .= '<tbody>';

            foreach ($payments as $payment) {
                $content .= '<tr>';
                $content .= '<td>' . $payment->user_name . '</td>';
                $content .= '<td>' . $payment->user_phone . '</td>'; // Display Guardian Name
                $content .= '<td>' . $payment->amount . '</td>';
                $content .= '<td>' . $payment->currency . '</td>';
                $content .= '<td>' . $payment->gateway . '</td>';
                $content .= '<td>' . $payment->payment_date . '</td>';
                $content .= '<td>' . $payment->paymentarea . '</td>';
                $content .= '</tr>';
            }

            $content .= '</tbody>';
            $content .= '</table>';
        } catch (Exception $e) {
            $content .= '<div>Error fetching payments: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }

        $this->content = new stdClass();
        $this->content->text = $content;
        $this->content->footer = 'This is the footer'; // Replace with appropriate footer content

        // Include necessary JavaScript for form handling
        $this->content->text .= "
            <script>
            </script>
        ";

        return $this->content;
    }
}
?>
