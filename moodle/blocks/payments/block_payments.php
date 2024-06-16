<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block to display payments.
 *
 * @package   block_payments
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

        // Button to trigger popup form
        $content .= html_writer::start_tag('div', ['class' => 'text-center']);
        $content .= html_writer::link('#', get_string('addpayment', 'block_payments'), [
            'class' => 'btn btn-primary',
            'id' => 'add-payment-button',
            'data-toggle' => 'modal',
            'data-target' => '#popup-modal'
        ]);
        $content .= html_writer::end_tag('div');

        // Popup form (modal)
        $content .= '<div id="popup-modal" class="modal fade" tabindex="-1" role="dialog">';
        $content .= '<div class="modal-dialog" role="document">';
        $content .= '<div class="modal-content">';
        $content .= '<div class="modal-header">';
        $content .= '<h5 class="modal-title">Add Payment</h5>';
        $content .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $content .= '<span aria-hidden="true">&times;</span>';
        $content .= '</button>';
        $content .= '</div>';
        $content .= '<div class="modal-body">';
        
        // Payment form
        $content .= '<form id="add-payment-form">';
        
        $content .= html_writer::start_tag('div', ['class' => 'form-group']);
        $content .= html_writer::label('Student Name', 'studentname');
        $content .= html_writer::tag('input', '', ['type' => 'text', 'class' => 'form-control', 'id' => 'studentname', 'required' => true]);
        $content .= html_writer::end_tag('div');
        
        $content .= html_writer::start_tag('div', ['class' => 'form-group']);
        $content .= html_writer::label('Amount Paid', 'amount');
        $content .= html_writer::tag('input', '', ['type' => 'number', 'class' => 'form-control', 'id' => 'amount', 'required' => true]);
        $content .= html_writer::end_tag('div');
        
        $content .= html_writer::start_tag('div', ['class' => 'form-group']);
        $content .= html_writer::label('Date', 'date');
        $content .= html_writer::tag('input', date('Y-m-d'), ['type' => 'text', 'class' => 'form-control', 'id' => 'date', 'readonly' => true]);
        $content .= html_writer::end_tag('div');
        
        $content .= '<button type="button" class="btn btn-primary" onclick="savePayment()">Save Payment</button>';
        $content .= '</form>';
        
        $content .= '</div>'; // modal-body
        $content .= '<div class="modal-footer">';
        $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
        $content .= '</div>'; // modal-footer
        $content .= '</div>'; // modal-content
        $content .= '</div>'; // modal-dialog
        $content .= '</div>'; // popup-modal

        // Display payments (For demonstration only, actual payments should be fetched from database)
        $content .= '<div>';
        $content .= '<h3>Recent Payments:</h3>';
        $content .= '<ul id="recent-payments-list"></ul>';
        $content .= '</div>';

        $this->content = new stdClass();
        $this->content->text = $content;
        $this->content->footer = 'This is the footer'; // Replace with appropriate footer content

        // Include necessary JavaScript for form handling
        $this->content->javascript = "
            <script>
                function savePayment() {
                    var studentName = $('#studentname').val();
                    var amount = $('#amount').val();
                    var date = $('#date').val();
                    
                    console.log('Student Name:', studentName);
                    console.log('Amount:', amount);
                    console.log('Date:', date);
                    
                    // Optionally, you can perform further actions here.
                    // For example, adding the payment details to a list on the page.
                    var paymentDetails = '<li><strong>' + studentName + '</strong> paid ' + amount + ' on ' + date + '</li>';
                    $('#recent-payments-list').prepend(paymentDetails);
                    
                    // Optionally, close the modal after submission
                    $('#popup-modal').modal('hide');
                }
            </script>
        ";

        return $this->content;
    }

}
