<?php
/*
Plugin Name: Footer Plugin
Description: Footer sample with form
Author: sona m s
*/
 
function footer_content() {
    echo '<footer style="background-color:black; color:#fff; padding:60px;">
            <div class="container">
                <p>This is footer.</p>
            </div>
          </footer>';
}
add_action('wp_footer', 'footer_content');
 
function create_form_submissions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'forms';
    $charset_collate = $wpdb->get_charset_collate();
 
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(15) NOT NULL,
        graduation_year int(4) NOT NULL,
        degree varchar(100) NOT NULL,
        college_name varchar(255) NOT NULL,
        resume_url varchar(255) NOT NULL,
        photo_url varchar(255) NOT NULL,
        submission_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
 
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_form_submissions_table');

function display_form_shortcode() {
    $success_message = isset($_GET['success']) && $_GET['success'] === 'true' ? '<p class="success-message">Form submitted successfully!</p>' : '';
    $error_message = isset($_GET['error']) && $_GET['error'] === 'true' ? '<p class="error-message">There was an error submitting the form. Please try again.</p>' : '';

    $image_url = 'http://localhost:10030/wp-content/uploads/2024/06/5114865.jpg';

    ob_start(); ?>
    <div class="form-container">
    <p>Contact Us </p>
        
        <?php echo $success_message; ?>
        <?php echo $error_message; ?>
        <div class="form-content">
            <div class="image-column">
                <img src="<?php echo esc_url($image_url); ?>" alt="Image">
            </div>
            <div class="form-column">
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="process_form_data">
                    
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required>
                    
                    <label for="graduation_year">Graduation Year:</label>
                    <input type="number" id="graduation_year" name="graduation_year" required>
                    
                    <label for="degree">Degree:</label>
                    <input type="text" id="degree" name="degree" required>
                    
                    <label for="college_name">College Name:</label>
                    <input type="text" id="college_name" name="college_name" required>
                    
                    <label for="resume">Resume:</label>
                    <input type="file" id="resume" name="resume" required>
                    
                    <label for="photo">Photo:</label>
                    <input type="file" id="photo" name="photo" required>
                    
                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>

    <style>
        .form-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            max-width: 600px;
            width:1500px;
            height:auto;
            margin: 0 auto;
        }

        .form-container p {
    text-align: center; 
    font-size: 40px; 
    font-weight: bold;
    font-family: 'Times New Roman', Times, serif;
}
        .form-content {
            display: flex;
            flex-direction: row;
        }

        .image-column {
            flex: 1;
            display: flex;
            justify-content:baseline;
            align-items:first baseline;
        }

        .image-column img {
            max-width: 100%;
            max-height: 80%;
            border-radius: 30%;
            height: 350px;
            width: 500px;
        }

        .form-column {
            flex: 1;
            padding-left: 30px;
        }

        .form-column label {
            display: block; 
            margin-bottom: 3px;
            color: black;
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold; 
        }

        .form-column input[type="text"],
        .form-column input[type="email"],
        .form-column input[type="number"],
        .form-column input[type="file"] {
            display: block; 
            width: 100%; 
            margin-bottom: 10px; 
            border: none; 
            background: transparent; 
            padding: 10px 0; 
            font-size: 16px; 
        }

        .form-column input[type="text"],
        .form-column input[type="email"],
        .form-column input[type="number"] {
            border-bottom: 1px solid black; 
        }

        .form-column input[type="submit"] {
            display: block; 
            width: 100%; 
            border: none; 
            padding: 10px; 
            background-color: navy;
            border-radius: 40px;
            color: white;
            cursor: pointer;
            font-size: 16px; 
        }

        .form-column input:focus {
            outline: none; 
        }
    </style>

    <?php return ob_get_clean();
}



add_shortcode('display_form', 'display_form_shortcode');

function process_form_data() {
    global $wpdb;
 
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $graduation_year = isset($_POST['graduation_year']) ? intval($_POST['graduation_year']) : '';
    $degree = isset($_POST['degree']) ? sanitize_text_field($_POST['degree']) : '';
    $college_name = isset($_POST['college_name']) ? sanitize_text_field($_POST['college_name']) : '';
    
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
 
    $resume_url = '';
    $photo_url = '';
 
    if (isset($_FILES['resume'])) {
        $uploadedfile = $_FILES['resume'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
 
        if ($movefile && !isset($movefile['error'])) {
            $resume_url = $movefile['url'];
        } else {
            wp_redirect(add_query_arg('error', 'true', $_SERVER['HTTP_REFERER']));
            exit;
        }
    }
 
    if (isset($_FILES['photo'])) {
        $uploadedfile = $_FILES['photo'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
 
        if ($movefile && !isset($movefile['error'])) {
            $photo_url = $movefile['url'];
        } else {
            wp_redirect(add_query_arg('error', 'true', $_SERVER['HTTP_REFERER']));
            exit;
        }
    }
 
    $submission_date = current_time('mysql');
    $table_name = $wpdb->prefix . 'forms';
    $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'graduation_year' => $graduation_year,
            'degree' => $degree,
            'college_name' => $college_name,
            'resume_url' => $resume_url,
            'photo_url' => $photo_url,
            'submission_date' => $submission_date
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );
 
    $admin_email = get_option('admin_email');
    $subject_admin = 'New Form Submission';
    $body_admin = "A new form submission has been received from $name.\n\nName: $name\nEmail: $email\nPhone: $phone\nGraduation Year: $graduation_year\nDegree: $degree\nCollege Name: $college_name\nResume: $resume_url\nPhoto: $photo_url\n";
    wp_mail($admin_email, $subject_admin, $body_admin);
 
    $subject_client = 'Thank you for your submission';
    $body_client = "Dear $name,\n\nThank you for your submission. We will get back to you as soon as possible.\n\nBest regards,\n[Your Website]";
    wp_mail($email, $subject_client, $body_client);
 
    wp_redirect(add_query_arg('success', 'true', $_SERVER['HTTP_REFERER']));
    exit;
}
add_action('admin_post_process_form_data', 'process_form_data');
add_action('admin_post_nopriv_process_form_data', 'process_form_data');
 
function display_form_data_shortcode() {
    global $wpdb;
 
    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    $table_name = $wpdb->prefix . 'forms';
 
    if (!empty($search_query)) {
        $submissions = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE name LIKE %s OR email LIKE %s OR phone LIKE %s OR graduation_year LIKE %s OR degree LIKE %s OR college_name LIKE %s", "%$search_query%", "%$search_query%", "%$search_query%", "%$search_query%", "%$search_query%", "%$search_query%")
        );
    } else {
        $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC");
    }
 
    ob_start(); ?>
    <div class="form-data-container">
        <h1>Form Submissions</h1>
        <form method="post" action="" class="search-form">
            <input type="text" name="search_query" placeholder="Search..." value="<?php echo esc_attr($search_query); ?>">
            <input type="submit" value="Search">
        </form>
        <?php
        if (!empty($submissions)) {
            echo '<div class="submitted-details">';
            echo '<ul>';
            foreach ($submissions as $submission) {
                echo '<li>';
                echo '<strong>Name:</strong> ' . esc_html($submission->name) . '<br>';
                echo '<strong>Email:</strong> ' . esc_html($submission->email) . '<br>';
                echo '<strong>Phone:</strong> ' . esc_html($submission->phone) . '<br>';
                echo '<strong>Graduation Year:</strong> ' . esc_html($submission->graduation_year) . '<br>';
                echo '<strong>Degree:</strong> ' . esc_html($submission->degree) . '<br>';
                echo '<strong>College Name:</strong> ' . esc_html($submission->college_name) . '<br>';
                echo '<a href="' . esc_url($submission->resume_url) . '" target="_blank">View Resume</a><br>';
                echo '<strong>Photo:</strong> <a href="' . esc_url($submission->photo_url) . '" target="_blank">View Photo</a><br>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<p>No submissions found.</p>';
        }
        ?>
    </div>
    <style>
        .form-data-container {
            margin: 20px;
        }
        .search-form input[type="text"] {
            padding: 10px;
            margin-right: 10px;
        }
        .search-form input[type="submit"] {
            padding: 10px;
        }
        .submitted-details ul {
            list-style: none;
            padding: 0;
        }
        .submitted-details li {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('display_form_data', 'display_form_data_shortcode');
 
add_action('phpmailer_init', 'configure_smtp');
function configure_smtp($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = 'sonamenothil05@gmail.com';
    $phpmailer->Password   = 'ffwn svhy pynz ovpk';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->From       = 'sonamenothil05@gmail.com';
    $phpmailer->FromName   = 'Sona M S';
}
 
function add_dashboard_menu_items() {
    add_menu_page(
        'Contact Form',
        'Contact Form',
        'manage_options',
        'contact_form_page',
        'display_contact_form_page',
        'dashicons-email',
        25
    );
}
add_action('admin_menu', 'add_dashboard_menu_items');
 
function display_contact_form_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'forms';
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC"); ?>
    <div class="form-data-container">
        <h1>Contact Form Submissions</h1>
        <?php if (!empty($submissions)) : ?>
            <table class="submissions-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Submission Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission) : ?>
                        <tr onclick="toggleDetails(this)">
                            <td><?php echo esc_html($submission->name); ?></td>
                            <td><?php echo esc_html($submission->submission_date); ?></td>
                        </tr>
                        <tr class="hidden details-row">
                            <td colspan="2">
                                <strong>Email:</strong> <?php echo esc_html($submission->email); ?><br>
                                <strong>Phone:</strong> <?php echo esc_html($submission->phone); ?><br>
                                <strong>Graduation Year:</strong> <?php echo esc_html($submission->graduation_year); ?><br>
                                <strong>Degree:</strong> <?php echo esc_html($submission->degree); ?><br>
                                <strong>College Name:</strong> <?php echo esc_html($submission->college_name); ?><br>
                                <strong>Resume:</strong> <a href="<?php echo esc_url($submission->resume_url); ?>" target="_blank">View Resume</a><br>
                                <strong>Photo:</strong> <a href="<?php echo esc_url($submission->photo_url); ?>" target="_blank">View Photo</a><br>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No form submissions found.</p>
        <?php endif; ?>
    </div>
    <style>
        .form-data-container {
            margin: 20px;
            font-family: 'Times New Roman', Times, serif;
        }
        .submissions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 15px;
        }
        .submissions-table th, .submissions-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .submissions-table th {
            background-color: lightgray;
            font-weight: bold;
        }
        .submissions-table tr:hover {
            background-color: lightsteelblue;
            cursor: pointer;
        }
        .hidden {
            display: none;
        }
      .details-row td {
    background-color: #f9f9f9;
    border-bottom: 1px solid #ddd;
    padding: 15px;
    font-size: 14px;
    line-height: 1.4;
}
 
.details-row td strong {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
 
.details-row td a {
    color: #007bff;
    text-decoration: none;
}
 
.details-row td a:hover {
    text-decoration: underline;
}
 
    </style>
    <script>
        function toggleDetails(row) {
            const nextRow = row.nextElementSibling;
            if (nextRow && nextRow.classList.contains('details-row')) {
                nextRow.classList.toggle('hidden');
            }
        }
    </script>
    <?php
}
?>