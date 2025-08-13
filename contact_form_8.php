<?php
/*
Plugin Name: Contact Form 8
Description: Simple contact form with admin submissions list.
Version: 1.1
Author: Sajid Ashraf
*/

// -----------------------------
// 1. ADD MENU TO WORDPRESS ADMIN
// -----------------------------
add_action('admin_menu', 'contact_form_menu');

function contact_form_menu() {
    add_menu_page(
        'Contact Form 8',
        'Contact Form 8',
        'manage_options',
        'contact_form_8',
        'form_setting',
        'dashicons-email',
        20
    );
}

// -----------------------------
// 2. ADMIN PAGE CONTENT
// -----------------------------
function form_setting() {
    echo '<h1>Contact Form 8 - Submissions</h1>';

    $submissions = get_option('cf8_submissions', []);

    if (!empty($submissions)) {
        echo '<table style="border-collapse: collapse; width: 100%; border: 1px solid #ccc;">';
        echo '<tr style="background: #0073aa; color: white;">
                <th style="padding: 4px;">First Name</th>
                <th style="padding: 4px;">Last Name</th>
                <th style="padding: 4px;">Email</th>
                <th style="padding: 4px;">Phone</th>
                <th style="padding: 4px;">Address</th>
                <th style="padding: 4px;">Message</th>
                <th style="padding: 4px;">Date</th>
              </tr>';

        foreach ($submissions as $s) {
            echo '<tr>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['first_name']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['last_name']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['email']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['phone']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['address']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['message']) . '</td>';
            echo '<td style="padding: 4px; border: 1px solid #ccc;">' . esc_html($s['date']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No submissions yet.</p>';
    }
}

// -----------------------------
// 3. LOAD CSS
// -----------------------------
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'cf8-style',
        plugin_dir_url(__FILE__) . 'includes/style.css'
    );
});

// -----------------------------
// 4. SHORTCODE FORM
// -----------------------------
add_shortcode('cf8_form', function() {
    ob_start();
    ?>
    <form method="post" class="sajid-contact-form">
        <p>
            <label>First Name:</label>
            <input type="text" name="cf8_first_name" required>
        </p>
        <p>
            <label>Last Name:</label>
            <input type="text" name="cf8_last_name" required>
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
        <p>
            <?php echo $submitted; ?>
        </p>
    </form>
    <?php
    return ob_get_clean();
});

// -----------------------------
// 5. HANDLE FORM SUBMISSION
// -----------------------------
add_action('wp', function() {
    if (isset($_POST['cf8_submit'])) {
        $submissions = get_option('cf8_submissions', []);

        $submissions[] = [
            'first_name' => sanitize_text_field($_POST['cf8_first_name']),
            'last_name'  => sanitize_text_field($_POST['cf8_last_name']),
            'email'      => sanitize_email($_POST['cf8_email']),
            'phone'      => sanitize_text_field($_POST['cf8_phone']),
            'address'    => sanitize_textarea_field($_POST['cf8_address']),
            'message'    => sanitize_textarea_field($_POST['cf8_message']),
            'date'       => date('Y-m-d H:i:s')
        ];

        update_option('cf8_submissions', $submissions);

        echo $submitted = "<p style='color:green; font-weight: bold;'>✅ Thank you! Your message has been sent.</p>";
    }
});
