<?php


error_reporting(E_ALL);
ob_start("ob_gzhandler");
include "Functions.php";
$conn = openConnection();
$iconn = openIConnection();
$query = "SELECT LockDate, MACAddress, MealCouponPrinterName, CompanyName, MealCouponFont, IDColumnName, PhoneColumnName FROM OtherSettingMaster";
$main_result = selectData($conn, $query);
$txtLockDate = $main_result[0];
$txtMACAddress = $main_result[1];
$txtMealCouponPrinterName = $main_result[2];
$txtCompany = $main_result[3];
$txtMealCouponFont = $main_result[4];
$txtID = $main_result[5];
$txtPhone = $main_result[6];
if (checkMAC($conn) == false) {
    print "Un Registered Application. Process Terminated.";
    exit;
}
while (true) {
    $query = "SELECT tenter.e_date, tenter.e_time, tuser.id, tuser.name, tuser.dept, tuser.company, tgroup.name, tgate.name, tenter.ed, tuser.remark, tuser.idno, tuser.phone FROM tuser, tgroup, tgate, tenter WHERE tenter.e_id = tuser.id AND tenter.e_group = tgroup.id AND tenter.g_id = tgate.id AND tgate.Meal = 1 AND tenter.p_flag = 0";
    $result = mysqli_query($conn, $query);
    while ($cur = mysqli_fetch_row($result)) {
        printMealCoupon($txtMealCouponPrinterName, $txtCompany, "Meal Coupon", $cur[0], $cur[1], $cur[2], $cur[3], $cur[4], $cur[5], $cur[6], $cur[7], $cur[8], $cur[9], $cur[10], $cur[11], $txtMealCouponFont, $txtID, $txtPhone);
        $query = "UPDATE tenter SET p_flag = 1 WHERE ed = '" . $cur[8] . "'";
        updateIData($iconn, $query, true);
    }
}
function printMealCoupon($txtMealCouponPrinterName, $txtCompany, $txtMealSlot, $txtDate, $txtTime, $txtID, $txtName, $txtDept, $txtDiv, $txtShift, $txtTerminal, $ed, $txtRmk, $txtIData, $txtPhoneData, $a, $txt_ID, $txtPhone)
{
    $printer = printer_open($txtMealCouponPrinterName);
    printer_start_doc($printer, "MealCoupon" . $ed);
    printer_start_page($printer);
    $font = printer_create_font("Arial", $a, $a / 2, PRINTER_FW_NORMAL, false, true, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, $txtCompany, 1, 1);
    printer_delete_font($font);
    $font = printer_create_font("Arial", $a, $a / 2, PRINTER_FW_NORMAL, true, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Meal Coupon", 1, $a * 2);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, displayDate($txtDate) . " - " . displayVirdiTime($txtTime), 0, $a / 2 * 7);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Trml: " . $txtTerminal, 1, $a / 2 * 8);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Emp ID: " . $txtID, 1, $a / 2 * 9);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Name: " . $txtName, 1, $a / 2 * 10);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Dept: " . $txtDept, 1, $a / 2 * 11);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Div/Title: " . $txtDiv, 1, $a / 2 * 12);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Rmk: " . $txtRmk, 1, $a / 2 * 13);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, $txt_ID . ": " . $txtIData, 1, $a / 2 * 14);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, $txtPhone . ": " . $txtPhoneData, 1, $a / 2 * 15);
    printer_delete_font($font);
    $font = printer_create_font("Verdana", $a / 2, $a / 4, PRINTER_FW_MEDIUM, false, false, false, 0);
    printer_select_font($printer, $font);
    printer_draw_text($printer, "Shift: " . $txtShift, 1, $a / 2 * 16);
    printer_delete_font($font);
    printer_end_page($printer);
    printer_end_doc($printer);
    printer_close($printer);
}

?>