<?php
/*
Plugin Name: Contact Form 8
Description: Simple contact form with admin submissions list.
Version: 1.1
Author: Sajid Ashraf
*/

/*
|--------------------------------------------------------------------------
| 1. ADD MENU TO WORDPRESS ADMIN
|--------------------------------------------------------------------------
This section adds a new menu item in the WordPress admin dashboard so you
can view form submissions.
*/
add_action('admin_menu', 'contact_form_menu');

function contact_form_menu() {
    add_menu_page(
        'Contact Form 8',        // Page title (shown in browser tab)
        'Contact Form 8',        // Menu title (shown in WP admin menu)
        'manage_options',        // Permission required
        'contact_form_8',        // Menu slug
        'form_setting',          // Callback function to display page content
        'dashicons-email',       // Icon in admin menu
        20                       // Menu position
    );
}

/*
|--------------------------------------------------------------------------
| 2. ADMIN PAGE CONTENT
|--------------------------------------------------------------------------
This function shows all saved form submissions in a table inside the admin page.
*/
function form_setting() {
    echo '<h1>Contact Form 8 - Submissions</h1>';

    // Get saved submissions from the database (option table)
    $submissions = get_option('cf8_submissions', []);

    if (!empty($submissions)) {
        // Start HTML table
        echo '<table style="border-collapse: collapse; width: 90%; border: 1px solid #ccc;">';
        echo '<tr style="background: #0073aa; color: white;">
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Sr</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Name</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Email</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Phone</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Address</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Message</th>
                <th style="padding: 6px; text-align: left; border-right: 1px solid #ccc;">Date</th>
              </tr>';

        // Loop through each submission and output in table rows
        $count = 0;
        foreach ($submissions as $s) {
            $count++;
            echo '<tr>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . $count . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['first_name']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['email']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['phone']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['address']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['message']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['date']) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        // If there are no submissions yet
        echo '<p>No submissions yet.</p>';
    }
}

/*
|--------------------------------------------------------------------------
| 3. LOAD CSS FILE FOR FRONTEND
|--------------------------------------------------------------------------
We enqueue a CSS file located in the "includes" folder for styling the form.
*/
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'cf8-style', // Handle name for the CSS
        plugin_dir_url(__FILE__) . 'includes/style.css' // File path
    );
});

/*
|--------------------------------------------------------------------------
| 4. INITIALIZE SUBMISSION MESSAGE VARIABLE
|--------------------------------------------------------------------------
We store a confirmation message in this variable after form submission.
*/
$submitted = "";

/*
|--------------------------------------------------------------------------
| 5. HANDLE FORM SUBMISSION
|--------------------------------------------------------------------------
This runs when the form is submitted. It saves data in the WordPress
database using update_option().
*/
add_action('wp', function() {
    global $submitted;

    // Check if the form submit button was clicked
    if (isset($_POST['cf8_submit'])) {
        // Get existing submissions (if any)
        $submissions = get_option('cf8_submissions', []);

        // Add new submission data
        $submissions[] = [
            'first_name' => sanitize_text_field($_POST['cf8_first_name']),
            'email'      => sanitize_email($_POST['cf8_email']),
            'phone'      => sanitize_text_field($_POST['cf8_phone']),
            'address'    => sanitize_textarea_field($_POST['cf8_address']),
            'message'    => sanitize_textarea_field($_POST['cf8_message']),
            'date'       => date('Y-m-d H:i:s')
        ];

        // Save updated submissions to database
        update_option('cf8_submissions', $submissions);

        // Set success message
        $submitted = "<p style='color:green; font-size: 16px;'>✅ Thank you! Your message has been sent.</p>";
    }
});

/*
|--------------------------------------------------------------------------
| 6. SHORTCODE FOR THE FORM
|--------------------------------------------------------------------------
This creates a shortcode [cf8_form] so the form can be displayed anywhere.
We also remove wpautop filter to prevent WordPress from adding unwanted <br> tags.
*/
add_shortcode('cf8_form', function() {
    // Remove automatic paragraph and line-break formatting
    remove_filter('the_content', 'wpautop');

    ob_start();
    global $submitted;
    ?>
    <form method="post" class="sajid-contact-form">
        <p>
            <label>Name:</label>
            <input type="text" name="cf8_first_name" required>
        </p>
        <p>
            <label>Email:</label>
            <input type="email" name="cf8_email" required>
        </p>
        <p>
            <label>Phone:</label>
            <input type="text" name="cf8_phone" required>
        </p>
        <p>
            <label>Address:</label>
            <input type="text" name="cf8_address" required>
        </p>
        <p>
            <label>Message:</label>
            <textarea name="cf8_message" required rows="6"></textarea>
        </p>
        <p>
            <input type="submit" name="cf8_submit" value="Send">
        </p>
        <p><?php echo $submitted; ?></p>
    </form>
    <?php
    return ob_get_clean();
});
remove_filter('the_content', 'wpautop');

?>