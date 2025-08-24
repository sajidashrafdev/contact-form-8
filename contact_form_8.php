<?php

/*
Plugin Name: Contact Form 8
Description: A simple contact form plugin for WordPress.
Version: 1.0.0
Author: Sajid Ashraf
*/

// Function to display the main menu and submenu on sidebar in dashboard
add_action('admin_menu', 'contact_form_8_menu');

function contact_form_8_menu()
{
    add_menu_page(
        'Contact Form 8',        // Page title (shown in browser tab)
        'Contact Form 8',        // Menu title (shown in WP admin menu)
        'manage_options',        // Permission required
        'contact_form_8',        // Menu slug
        'contact_form_8_page',   // Callback function to display page content
        'dashicons-email',       // Icon in admin menu
        20                       // Menu position
    );

    add_submenu_page(
        'contact_form_8',        // Parent slug
        'Add New Form',          // Page title
        'Add New Form',          // Menu title
        'manage_options',        // Capability
        'add_new_form',          // Menu slug
        'add_new_form_page'      // Callback function to display page content
    );
}


function contact_form_8_page()
{
    // Main page content for Contact Form 8
?>
    <div class="wrap">
        <h1>Contact Form 8</h1>
        <h4>Welcome to the Contact Form 8 plugin!</h4>
        <h2>View submitted forms below:</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $form_data = get_option('contact_form_8_data');
                if (!empty($form_data)) {
                    for ($i = 0; $i < count($form_data); $i++) {
                        echo '<tr>';
                        echo '<td>' . ($i + 1) . '</td>';
                        echo '<td>' . $form_data[$i]['name'] . '</td>';
                        echo '<td>' . $form_data[$i]['email'] . '</td>';
                        echo '<td>' . $form_data[$i]['message'] . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No submissions found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
}

function add_new_form_page()
{
    // Page content for adding a new form
    echo '<div class="wrap">';
    echo '<h1>Add New Form</h1>';
    echo '</div>';
}


// function to display the contact form in frontend using shortcode
function contact_form()
{
    $form = '<form method="post" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
            <input type="submit" value="Submit" name="submit">
            </form>';

    return $form;
}

// creating shortcode for contact form [contact_form_8]
add_shortcode('contact_form_8', 'contact_form');


// Action hook to handle form submission
add_action('init', 'handle_contact_form_submission');
function handle_contact_form_submission()
{
    if (isset($_POST['submit'])) {

        // fetch form data
        $name = $_POST['name'];
        $email = $_POST['email'];
        $message = $_POST['message'];

        // Fetch existing form data
        $form_data = get_option('contact_form_8_data');
        // Check if form data is not an array
        if (!is_array($form_data)) {
            // If not, initialize it as an empty array
            $form_data = [];
        }
        // storing form data into array
        $form_data[] = array(
            'name'    => $name,
            'email'   => $email,
            'message' => $message
        );

        // Storing form data in the database
        update_option('contact_form_8_data', $form_data);

        // Send email notification
        $to = get_option('admin_email');               // Admin email address
        $subject = 'New Contact Form Submission';       // Email subject
        $body = "Name: $name\nEmail: $email\nMessage: $message"; // Email body

        // we use wp_mail() function to send email
        wp_mail($to, $subject, $body);
    }
}

?>