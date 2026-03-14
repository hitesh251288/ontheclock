<?php


class FTP
{
    public static function download($local_dir, $remote_dir, $ftp_conn)
    {
        if ($remote_dir != ".") {
            if (ftp_chdir($ftp_conn, $remote_dir) == false) {
                echo "" . "Change Dir Failed: " . $dir . "<br />\r\n";
                return NULL;
            }
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            chdir($dir);
        }
        $contents = ftp_nlist($ftp_conn, ".");
        foreach ($contents as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }
            if (@ftp_chdir($ftp_conn, $file)) {
                ftp_chdir($ftp_conn, "..");
                FTP::download($local_dir, $file, $ftp_conn);
            } else {
                ftp_get($ftp_conn, "" . $local_dir . "/" . $file, $file, FTP_BINARY);
            }
        }
        ftp_chdir($ftp_conn, "..");
        chdir("..");
    }
}

?>