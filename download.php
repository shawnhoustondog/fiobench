<?php
if(isset($_REQUEST["file"])){
    // Get parameters
    $file = urldecode($_REQUEST["file"]); // Decode URL-encoded string

    /* Test whether the file name contains illegal characters
    such as "../" using the regular expression */
    if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $file)){
        preg_match('/[^-]*-(.*)/', $file, $filename);
        $filepath = "/opt/app-root/files/" . $file;
        $mimetype = "application/octet-stream";
        if( $temp = mime_content_type($filepath) )
        {
            $mimetype = $temp;
        }

        // Process download
        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mimetype);
            header('Content-Disposition: attachment; filename="'. $filename[1] . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            die();
        } else {
            http_response_code(404);
	        die();
        }
    } else {
        die("Invalid file name!");
    }
}
?>
