<?php
// Check if there are any success messages
if (isset($success_msg)) {
    foreach ($success_msg as $msg) {
        echo '<script>swal("' . htmlspecialchars($msg) . '", "", "success");</script>';
    }
}

// Check if there are any warning messages
if (isset($warning_msg)) {
    foreach ($warning_msg as $msg) {
        echo '<script>swal("' . htmlspecialchars($msg) . '", "", "warning");</script>';
    }
}

// Check if there are any info messages
if (isset($info_msg)) {
    foreach ($info_msg as $msg) {
        echo '<script>swal("' . htmlspecialchars($msg) . '", "", "info");</script>';
    }
}

// Check if there are any error messages
if (isset($error_msg)) {
    foreach ($error_msg as $msg) {
        echo '<script>swal("' . htmlspecialchars($msg) . '", "", "error");</script>';
    }
}
?>
