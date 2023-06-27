<?php

namespace Src\Controller;

use Src\System\DatabaseMethods;
use Src\Controller\ExposeDataController;
use Src\Controller\PaymentController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminController
{
    private $dm = null;
    private $expose = null;

    public function __construct()
    {
        $this->dm = new DatabaseMethods();
        $this->expose = new ExposeDataController();
    }

    public function processVendorPay($data)
    {
        $payConfirm = new PaymentController();
        return $payConfirm->vendorPaymentProcess($data);
    }

    public function fetchVendorUsernameByUserID(int $user_id)
    {
        $query = "SELECT user_name FROM sys_users AS su, vendor_details AS vd WHERE su.id = vd.user_id AND vd.id = :ui";
        return $this->dm->getData($query, array(':ui' => $user_id));
    }

    public function resetUserPassword($password)
    {

        // Hash password
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $query_result = $this->dm->inputData("UPDATE sys_users SET `password` = :pw WHERE id = :id", array(":pw" => $hashed_pw));

        if ($query_result) {
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "{$_SESSION["user"]} Updated user their account's password"
            );
            return array("success" => false, "message" => "Account's password reset was successful!");
        }
        return array("success" => false, "message" => "Failed to reset user account password!");
    }

    public function verifyAdminLogin($username, $password)
    {
        $sql = "SELECT * FROM `sys_users` WHERE `user_name` = :u";
        $data = $this->dm->getData($sql, array(':u' => $username));
        if (!empty($data)) {
            if (password_verify($password, $data[0]["password"])) {
                return $data;
            }
        }
        return 0;
    }

    public function getAcademicPeriod()
    {
        $query = "SELECT YEAR(`start_date`) AS start_year, YEAR(`end_date`) AS end_year, info 
                FROM admission_period WHERE active = 1";
        return $this->dm->getData($query);
    }

    public function getCurrentAdmissionPeriodID()
    {
        return $this->dm->getID("SELECT `id` FROM `admission_period` WHERE `active` = 1");
    }

    public function fetchPrograms(int $type)
    {
        $param = array();
        if ($type != 0) {
            $query = "SELECT * FROM programs WHERE `type` = :t";
            $param = array(':t' => $type);
        } else {
            $query = "SELECT * FROM programs";
        }
        return $this->dm->getData($query, $param);
    }

    public function getAvailableForms()
    {
        return $this->dm->getData("SELECT * FROM `forms`");
    }

    public function getFormCategories()
    {
        return $this->dm->getData("SELECT * FROM `form_categories`");
    }

    public function fetchUserName($user_id)
    {
        $sql = "SELECT CONCAT(SUBSTRING(`first_name`, 1, 1), '. ' , `last_name`) AS `userName` 
                FROM `sys_users` WHERE `id` = :u";
        return $this->dm->getData($sql, array(':u' => $user_id));
    }

    public function fetchFullName($user_id)
    {
        $sql = "SELECT CONCAT(`first_name`, ' ' , `last_name`) AS `fullName`, 
                user_name AS email_address, `role` AS user_role 
                FROM `sys_users` WHERE `id` = :u";
        return $this->dm->getData($sql, array(':u' => $user_id));
    }

    public function logActivity(int $user_id, $operation, $description)
    {
        $query = "INSERT INTO `activity_logs`(`user_id`, `operation`, `description`) VALUES (:u,:o,:d)";
        $params = array(":u" => $user_id, ":o" => $operation, ":d" => $description);
        $this->dm->inputData($query, $params);
    }
    // For admin settings


    /**
     * CRUD for form price
     */

    public function fetchAllFormPriceDetails()
    {
        $query = "SELECT f.id, f.name AS form_name, fc.name AS form_type_name, f.amount 
                FROM form_categories AS fc, forms AS f WHERE fc.id = f.form_category";
        return $this->dm->getData($query);
    }

    public function fetchFormPrice($form_price_id)
    {
        $query = "SELECT fp.id AS fp_id, ft.id AS ft_id, ft.name AS ft_name, fp.name AS fp_name, fp.amount 
                FROM form_categories AS ft, forms AS fp WHERE ft.id = fp.form_category AND fp.id = :i";
        return $this->dm->getData($query, array(":i" => $form_price_id));
    }

    public function addFormPrice($form_category, $form_name, $form_price)
    {
        $query = "INSERT INTO forms (form_category, `name`, amount) VALUES(:ft, :fn, :fp)";
        $params = array(":ft" => $form_category, ":fn" => $form_name, ":fp" => $form_price);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added new {$form_name} form costing {$form_price} to form type {$form_category}"
            );
        return $query_result;
    }

    public function updateFormPrice(int $form_id, $form_category, $form_name, $form_price)
    {
        $query = "UPDATE forms SET amount = :fp, form_category = :ft, `name` = :fn WHERE id = :i";
        $params = array(":i" => $form_id, ":ft" => $form_category, ":fn" => $form_name, ":fp" => $form_price);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated {$form_name} form costing {$form_price} to form type {$form_category}"
            );
        return $query_result;
    }

    public function deleteFormPrice($form_price_id)
    {
        $query = "DELETE FROM forms WHERE id = :i";
        $params = array(":i" => $form_price_id);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted form with id {$form_price_id}"
            );
        return $query_result;
    }

    /**
     * CRUD for vendor
     */

    public function fetchAllVendorsMainBranch()
    {
        return $this->dm->getData("SELECT * FROM vendor_details WHERE `type` <> 'ONLINE' AND branch = 'MAIN'");
    }

    public function fetchVendorsBranches($company)
    {
        return $this->dm->getData("SELECT * FROM vendor_details WHERE `company` = :c", array(":c" => $company));
    }

    public function fetchAllVendorDetails()
    {
        return $this->dm->getData("SELECT * FROM vendor_details WHERE `type` <> 'ONLINE'");
    }

    public function fetchVendor(int $vendor_id)
    {
        $query = "SELECT vd.*, su.first_name, su.last_name, su.user_name 
                    FROM vendor_details AS vd, sys_users AS su WHERE vd.id = :i AND vd.user_id = su.id";
        return $this->dm->inputData($query, array(":i" => $vendor_id));
    }

    public function fetchVendorSubBranchesGrp($company)
    {
        $query = "SELECT * FROM vendor_details WHERE company = :c AND 
                branch <> 'MAIN' AND `type` <> 'ONLINE' GROUP BY `branch`";
        return $this->dm->inputData($query, array(":c" => $company));
    }

    public function fetchVendorSubBranches($company)
    {
        $query = "SELECT * FROM vendor_details WHERE branch = :b AND 
                branch <> 'MAIN' AND `type` <> 'ONLINE'";
        return $this->dm->inputData($query, array(":b" => $company));
    }

    public function verifyVendorByCompanyAndBranch($company, $branch)
    {
        $query = "SELECT `id` FROM `vendor_details` WHERE `company` = :c AND `branch` = :b";
        return $this->dm->inputData($query, array(":c" => $company, ":b" => $branch));
    }

    public function verifySysUserExists($email)
    {
        $query = "SELECT `id` FROM `sys_users` WHERE `username` = :u";
        return $this->dm->inputData($query, array(":u" => $email));
    }

    /*public function addVendor($v_name, $v_email, $v_phone, $branch)
    {
        // verify if a vendor with this email exists
        if ($this->verifyVendorSysUserExists($v_email)) {
            return array("success" => false, "message" => "A user with this email exists already exists!");
        }



        // if not prepare query and save the details 
        $query1 = "INSERT INTO vendor_details (`id`, `type`, `company`, `branch`, `phone_number`) VALUES(:id, :tp, :nm, :b, :pn)";
        $vendor_id = time();
        $params1 = array(":id" => $vendor_id, ":tp" => "VENDOR", ":nm" => $v_name, ":b" => $branch, ":pn" => $v_phone);

        if ($this->dm->inputData($query1, $params1)) {

            $password = $this->expose->genVendorPin();
            $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

            $query2 = "INSERT INTO vendor_login (`user_name`, `password`, `vendor`) VALUES(:un, :pw, :vi)";
            $params2 = array(":un" => sha1($v_email), ":pw" => $hashed_pw, ":vi" => $vendor_id);
            $query_result = $this->dm->inputData($query2, $params2);

            if ($query_result)
                $this->logActivity(
                    $_SESSION["user"],
                    "INSERT",
                    "Added vendor {$vendor_id} with username/email {$v_email}"
                );

            $subject = "RMU Vendor Registration";
            $message = "<p>Hi," . $v_name . " </p></br>";
            $message .= "<p>Your account to access RMU Admissions Portal as a vendor was successful.</p>";
            $message .= "<p>Find below your Login details.</p></br>";
            $message .= "<p style='font-weight: bold;'>Username: " . $v_email . "</p>";
            $message .= "<p style='font-weight: bold;'>Password: " . $password . "</p></br>";
            $message .= "<div>Please note the following: </div>";
            $message .= "<ol style='color:red; font-weight:bold;'>";
            $message .= "<li>Don't let anyone see your login password</li>";
            $message .= "<li>Access the portal and change your password</li>";
            $message .= "</ol></br>";
            $message .= "<p><a href='forms.rmuictonline.com/buy-vendor/'>Click here</a> to access portal.</ol>";

            return $this->expose->sendEmail($v_email, $subject, $message);
        }
        return 0;
    }*/
    public function saveDataFile($fileObj)
    {
        $allowedFileType = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($fileObj["type"], $allowedFileType)) {
            return array("success" => false, "message" => "Invalid file type. Please choose an excel file!");
        }

        if ($fileObj['error'] == UPLOAD_ERR_OK) {

            // Create a unique file name
            $name = time() . '-' . 'awaiting.xlsx';

            // Create the full path to the file
            $targetPath = UPLOAD_DIR . "/branches/" . $name;

            // Delete file if exsists
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }

            // Move the file to the target directory
            if (!move_uploaded_file($fileObj['tmp_name'], $targetPath))
                return array("success" => false, "message" => "Failed to upload file!");
            return array("success" => true, "message" => $targetPath);
        }
        return array("success" => false, "message" => "Error: Invalid file object!");
    }

    public function uploadCompanyBranchesData($mainBranch, $fileObj)
    {
        // save file to uploads folder
        $file_upload_msg = $this->saveDataFile($fileObj);
        if (!$file_upload_msg["success"]) return $file_upload_msg;

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadSheet = $reader->load($file_upload_msg["message"]);
        $excelSheet = $spreadSheet->getActiveSheet();
        $spreadSheetArray = $excelSheet->toArray();

        $startRow = 1;
        $endRow = count($spreadSheetArray);

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = array();

        $privileges = array("select" => 1, "insert" => 1, "update" => 0, "delete" => 0);

        for ($i = $startRow; $i <= $endRow - 1; $i++) {
            $v_branch = $spreadSheetArray[$i][0];
            $v_email = $spreadSheetArray[$i][1];
            $v_phone = $spreadSheetArray[$i][2];
            $v_role = $spreadSheetArray[$i][3];

            if (!$v_branch || !$v_email || !$v_phone) {
                array_push($skippedCount, ($i - 1));
                continue;
            }

            $user_data = array(
                "first_name" => $mainBranch, "last_name" => $v_branch, "user_name" => $v_email,
                "user_role" => "Vendors", "vendor_company" => $mainBranch,
                "vendor_phone" => $v_phone, "vendor_branch" => $v_branch, "vendor_role" => $v_role
            );

            $vendor_id = time() + $i;
            if ($this->addSystemUser($user_data, $privileges, $vendor_id)) $successCount += 1;
            else $errorCount += 1;
        }
        return array(
            "success" => true,
            "message" => "Successfully added MAIN branch account and {$successCount} other branches with {$errorCount} unsuccessful!. Skipped rows are " . json_encode($skippedCount)
        );
    }

    public function updateVendor($v_id, $v_email, $v_phone)
    {
        $query1 = "UPDATE vendor_details SET `phone_number` = :pn WHERE id = :id";
        $params1 = array(":id" => $v_id, ":pn" => $v_phone);
        if (!$this->dm->inputData($query1, $params1))
            return array("success" => false, "message" => "Failed to updated vendor's account information! [Error code 1]");

        $query2 = "UPDATE sys_users SET `user_name` = :ea WHERE id = :id";
        $params2 = array(":id" => $v_id, ":ea" => $v_email);
        if (!$this->dm->inputData($query2, $params2))
            return array("success" => false, "message" => "Failed to updated vendor's information! [Error code 2]");

        $this->logActivity($_SESSION["user"], "UPDATE", "Updated information for vendor {$v_id}");
        return array("success" => true, "message" => "Successfully updated vendor's account information!");
    }

    public function deleteVendor($vendor_id)
    {
        $vendor_info = $this->fetchVendor($vendor_id);
        $this->deleteSystemUser($vendor_info[0]["user_id"]);
        $query_result2 = $this->dm->inputData("DELETE FROM vendor_details WHERE id = :i", array(":i" => $vendor_id));

        if ($query_result2)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted vendor {$vendor_id} information"
            );
        return $query_result2;
    }

    /**
     * CRUD for programme
     */

    public function fetchAllPrograms()
    {
        $query = "SELECT p.`id`, p.`name`, f.name AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, forms AS f WHERE p.type = f.id";
        return $this->dm->getData($query);
    }

    public function fetchProgramme($prog_id)
    {
        $query = "SELECT p.`id`, p.`name`, f.id AS `type`, p.`weekend`, p.`group` 
                FROM programs AS p, forms AS f WHERE p.type = f.id AND p.id = :i";
        return $this->dm->getData($query, array(":i" => $prog_id));
    }

    public function addProgramme($prog_name, $prog_type, $prog_wkd, $prog_grp)
    {
        $query = "INSERT INTO programs (`name`, `type`, `weekend`, `group`) VALUES(:n, :t, :w, :g)";
        $params = array(":n" => strtoupper($prog_name), ":t" => $prog_type, ":w" => $prog_wkd, ":g" => $prog_grp);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added new programme {$prog_name} of programme type {$prog_type}"
            );
        return $query_result;
    }

    public function updateProgramme($prog_id, $prog_name, $prog_type, $prog_wkd, $prog_grp)
    {
        $query = "UPDATE programs SET `name` = :n, `type` = :t, `weekend` = :w, `group` = :g WHERE id = :i";
        $params = array(":n" => strtoupper($prog_name), ":t" => $prog_type, ":w" => $prog_wkd, ":g" => $prog_grp, ":i" => $prog_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated information for program {$prog_id}"
            );
        return $query_result;
    }

    public function deleteProgramme($prog_id)
    {
        $query = "DELETE FROM programs WHERE id = :i";
        $query_result = $this->dm->inputData($query, array(":i" => $prog_id));
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Deleted programme {$prog_id}"
            );
        return $query_result;
    }

    /**
     * CRUD for Admission Period
     */

    public function fetchAllAdmissionPeriod()
    {
        return $this->dm->getData("SELECT * FROM admission_period ORDER BY `end_date` ASC");
    }

    public function fetchCurrentAdmissionPeriod()
    {
        return $this->dm->getData("SELECT * FROM admission_period WHERE `active` = 1");
    }

    public function fetchAdmissionPeriod($adp_id)
    {
        $query = "SELECT * FROM admission_period WHERE id = :i";
        return $this->dm->inputData($query, array(":i" => $adp_id));
    }

    public function addAdmissionPeriod($adp_start, $adp_end, $adp_info)
    {
        $query = "INSERT INTO admission_period (`start_date`, `end_date`, `info`, `active`) 
                VALUES(:sd, :ed, :i, :a)";
        $params = array(":sd" => $adp_start, ":ed" => $adp_end, ":i" => $adp_info, ":a" => 1);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result) {
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Added admisiion period  with start date {$adp_start} and end date {$adp_end}"
            );

            return array("success" => true, "message" => "Successfully open new admission period!");
        }
        return array("success" => false, "message" => "Failed to open new admission period!");
    }

    public function updateAdmissionPeriod($adp_id, $adp_end, $adp_info)
    {
        $query = "UPDATE admission_period SET `end_date` = :ed, `info` = :i WHERE id = :id";
        $params = array(":ed" => $adp_end, ":i" => $adp_info, ":id" => $adp_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated information for admisiion period {$adp_id}"
            );
        return $query_result;
    }

    public function closeAdmissionPeriod($adp_id)
    {
        $query = "UPDATE admission_period SET active = 0 WHERE id = :i";
        $query_result = $this->dm->inputData($query, array(":i" => $adp_id));
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Closed admission with id {$adp_id}"
            );
        return $query_result;
    }


    /**
     * CRUD for user accounts
     */

    public function fetchAllNotVendorSystemUsers()
    {
        return $this->dm->getData("SELECT * FROM `sys_users` WHERE `role` <> 'Vendors'");
    }

    public function fetchAllSystemUsers()
    {
        return $this->dm->getData("SELECT * FROM `sys_users`");
    }

    public function fetchSystemUser($user_id)
    {
        $query = "SELECT u.*, p.`select`, p.`insert`, p.`update`, p.`delete` 
                FROM sys_users AS u, sys_users_privileges AS p 
                WHERE u.`id` = :i AND u.`id` = p.`user_id`";
        return $this->dm->inputData($query, array(":i" => $user_id));
    }

    public function verifySysUserByEmail($email)
    {
        $query = "SELECT `id` FROM `sys_users` WHERE `user_name` = :u";
        return $this->dm->inputData($query, array(":u" => $email));
    }

    public function addSystemUser($user_data, $privileges, $vendor_id = 0)
    {
        // verify if a vendor with this email exists
        if ($this->verifySysUserByEmail($user_data["user_name"])) {
            return array("success" => false, "message" => "This email ({$user_data['user_name']}) is associated with an account!");
        }

        // Generate password
        $password = $this->expose->genVendorPin();

        // Hash password
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

        // Create insert query
        $query1 = "INSERT INTO sys_users (`first_name`, `last_name`, `user_name`, `password`, `role`) VALUES(:fn, :ln, :un, :pw, :rl)";
        $params1 = array(
            ":fn" => $user_data["first_name"], ":ln" => $user_data["last_name"], ":un" => $user_data["user_name"],
            ":pw" => $hashed_pw, ":rl" => $user_data["user_role"]
        );

        // execute query
        $action1 = $this->dm->inputData($query1, $params1);
        if (!$action1) return array("success" => false, "message" => "Failed to create user account!");

        // verify and get user account info
        $sys_user = $this->verifyAdminLogin($user_data["user_name"], $password);
        if (empty($sys_user)) return array("success" => false, "message" => "Created user account, but failed to verify user account!");

        // Create insert query for user privileges
        $query2 = "INSERT INTO `sys_users_privileges` (`user_id`, `select`,`insert`,`update`,`delete`) 
                VALUES(:ui, :s, :i, :u, :d)";
        $params2 = array(
            ":ui" => $sys_user[0]["id"], ":s" => $privileges["select"], ":i" => $privileges["insert"],
            ":u" => $privileges["update"], ":d" => $privileges["delete"]
        );

        // Execute user privileges 
        $action2 = $this->dm->inputData($query2, $params2);
        if (!$action2) return array("success" => false, "message" => "Failed to create given roles for the user!");

        $subject = "Regional Maritime University - User Account";

        if (strtoupper($user_data["user_role"]) == "VENDORS") {
            if (!$vendor_id) $vendor_id = time();
            $query1 = "INSERT INTO vendor_details (`id`, `type`, `company`, `branch`, `role`, `phone_number`, `user_id`) 
                        VALUES(:id, :tp, :cp, :bh, :vr, :pn, :ui)";
            $params1 = array(
                ":id" => $vendor_id, ":tp" => "VENDOR", ":cp" => $user_data["vendor_company"],
                ":bh" => $user_data["vendor_branch"], ":vr" => $user_data["vendor_role"],
                ":pn" => $user_data["vendor_phone"], ":ui" => $sys_user[0]["id"]
            );
            $this->dm->inputData($query1, $params1);
            $subject = "Regional Maritime University - Vendor Account";
        }

        $this->logActivity(
            $_SESSION["user"],
            "INSERT",
            "Added new user account with username/email {$user_data["user_name"]}"
        );

        // Prepare email
        $message = "<p>Hi " . $user_data["first_name"] . " " . $user_data["last_name"] . ", </p></br>";
        $message .= "<p>Your account to access Regional Maritime University's Admissions Portal as a " . $user_data["user_role"] . " officer was created successfully.</p>";
        $message .= "<p>Find below your Login details.</p></br>";
        $message .= "<p style='font-weight: bold;'>Username: " . $user_data["user_name"] . "</p>";
        $message .= "<p style='font-weight: bold;'>Password: " . $password . "</p></br>";
        $message .= "<div>Please note the following: </div>";
        $message .= "<ol style='color:red; font-weight:bold;'>";
        $message .= "<li>Don't let anyone see your login password</li>";
        $message .= "<li>Access the portal and change your password</li>";
        $message .= "</ol></br>";
        $message .= "<p><a href='office.rmuictonline.com'>Click here to access portal</a>.</p>";

        // Send email
        $emailed = $this->expose->sendEmail($user_data["user_name"], $subject, $message);

        // verify email status and return result
        if ($emailed !== 1) return array(
            "success" => false,
            "message" => "Created user account, but failed to send email! Error: " . $emailed
        );

        return array("success" => true, "message" => "Successfully created user account!");
    }

    public function updateSystemUser($user_id, $first_name, $last_name, $email_addr, $role, $privileges)
    {
        $query = "UPDATE sys_users SET `user_name` = :un, `first_name` = :fn, `last_name` = :ln, `role` = :rl 
                WHERE id = :id";
        $params = array(
            ":un" => $email_addr, ":fn" => $first_name, ":ln" => $last_name,
            ":rl" => $role, ":id" => $user_id
        );
        if ($this->dm->inputData($query, $params)) {
            // Create insert query for user privileges
            $query2 = "UPDATE `sys_users_privileges` SET `select` = :s, `insert` = :i,`update` = :u, `delete`= :d 
                        WHERE `user_id` = :ui";
            $params2 = array(
                ":ui" => $user_id, ":s" => $privileges["select"], ":i" => $privileges["insert"],
                ":u" => $privileges["update"], ":d" => $privileges["delete"]
            );
            // Execute user privileges 
            $action2 = $this->dm->inputData($query2, $params2);
            if (!$action2) return array("success" => false, "message" => "Failed to update user account privileges!");

            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated user {$user_id} account information and privileges"
            );

            return array("success" => true, "message" => "Successfully updated user account information!");
        }
        return array("success" => false, "message" => "Failed to update user account information!");
    }

    public function changeSystemUserPassword($user_id, $email_addr, $first_name)
    {
        $password = $this->expose->genVendorPin();
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE sys_users SET `password` = :pw WHERE id = :id";
        $params = array(":id" => $user_id, ":pw" => $hashed_pw);
        $query_result = $this->dm->inputData($query, $params);

        if ($query_result) {

            $this->logActivity(
                $_SESSION["user"],
                "UPDATE",
                "Updated user {$user_id} account's password"
            );

            return $query_result;
            $subject = "RMU System User";
            $message = "<p>Hi " . $first_name . ", </p></br>";
            $message .= "<p>Find below your Login details.</p></br>";
            $message .= "<p style='font-weight: bold; font-size: 18px'>Username: " . $email_addr . "</p></br>";
            $message .= "<p style='font-weight: bold; font-size: 18px'>Password: " . $password . "</p></br>";
            $message .= "<p style='color:red; font-weight:bold'>Don't let anyone see your login password</p></br>";
            return $this->expose->sendEmail($email_addr, $subject, $message);
        }
        return 0;
    }

    public function deleteSystemUser($user_id)
    {
        $query = "DELETE FROM sys_users WHERE id = :i";
        $params = array(":i" => $user_id);
        $query_result = $this->dm->inputData($query, $params);
        if ($query_result)
            $this->logActivity(
                $_SESSION["user"],
                "DELETE",
                "Removed user {$user_id} accounts"
            );
        return $query_result;
    }

    // end of setups

    public function fetchAvailableformTypes()
    {
        return $this->dm->getData("SELECT * FROM forms");
    }

    public function getFormTypeName(int $form_id)
    {
        $query = "SELECT * FROM forms WHERE id = :i";
        return $this->dm->getData($query, array(":i" => $form_id));
    }

    public function getApplicantAppNum(int $app_num)
    {
        $query = "SELECT pd.`app_number` FROM `purchase_detail` AS pd, `applicants_login` AS al 
                WHERE pd.`id` = al.`purchase_id` AND al.`id` = :i";
        return $this->dm->getData($query, array(":i" => $app_num));
    }

    public function fetchAllAwaitingApplicationsBS()
    {
        $query = "SELECT pd.id AS AdmissionNumber, ab.index_number AS IndexNumber, 
                    ab.month_completed AS ExamMonth, ab.year_completed AS ExamYear 
                FROM 
                    applicants_login AS al, purchase_detail AS pd, 
                    admission_period AS ap, academic_background AS ab  
                WHERE
                    al.id = ab.app_login AND al.purchase_id = pd.id AND 
                    ap.id = pd.admission_period AND ab.awaiting_result = 1 AND ap.active = 1";
        return $this->dm->getData($query);
    }

    /**
     * Fetching forms sale data totals
     */

    public function fetchTotalFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
                FROM purchase_detail AS pd, forms AS ft, admission_period AS ap, vendor_details AS v  
                WHERE pd.form_id = ft.id AND pd.admission_period = ap.id AND pd.vendor = v.id AND ap.active = 1";
        return $this->dm->getData($query);
    }

    public function fetchTotalPostgradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, forms AS ft, admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_id = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND ap.active = 1 AND ft.name LIKE '%Post%' OR ft.name LIKE '%Master%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalUdergradsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, forms AS ft, admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_id = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND ap.active = 1 AND (ft.name LIKE '%Degree%' OR ft.name LIKE '%Diploma%')";
        return $this->dm->getData($query);
    }

    public function fetchTotalShortCoursesFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, forms AS ft, admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_id = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND ap.active = 1 AND ft.name LIKE '%Short%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalVendorsFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, forms AS ft, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_id = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND ap.active = 1 AND v.vendor_name NOT LIKE '%ONLINE%'";
        return $this->dm->getData($query);
    }

    public function fetchTotalOnlineFormsSold()
    {
        $query = "SELECT COUNT(pd.id) AS total 
        FROM 
            purchase_detail AS pd, forms AS ft, 
            admission_period AS ap, vendor_details AS v  
        WHERE
            pd.form_id = ft.id AND pd.admission_period = ap.id AND 
            pd.vendor = v.id AND ap.active = 1 AND v.vendor_name LIKE '%ONLINE%'";
        return $this->dm->getData($query);
    }

    /**
     * Fetching form sales data by statistics
     */
    public function fetchFormsSoldStatsByVendor()
    {
        $query = "SELECT 
                    v.vendor_name, COUNT(pd.id) AS total, SUM(ft.amount) AS amount 
                FROM 
                    purchase_detail AS pd, forms AS ft, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_id = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND ap.active = 1 
                GROUP BY pd.vendor";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByPaymentMethod()
    {
        $query = "SELECT 
                    pd.payment_method, COUNT(pd.id) AS total, SUM(ft.amount) AS amount 
                FROM 
                    purchase_detail AS pd, forms AS ft, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_id = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND ap.active = 1 
                GROUP BY pd.payment_method";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByFormType()
    {
        $query = "SELECT 
                    ft.name, COUNT(pd.id) AS total, SUM(ft.amount) AS amount 
                FROM 
                    purchase_detail AS pd, forms AS ft, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_id = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND ap.active = 1 
                GROUP BY pd.form_id";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByCountry()
    {
        $query = "SELECT 
                    pd.country_name, pd.country_code, COUNT(pd.id) AS total, SUM(ft.amount) AS amount 
                FROM 
                    purchase_detail AS pd, forms AS ft, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_id = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND ap.active = 1 
                GROUP BY pd.country_code";
        return $this->dm->getData($query);
    }

    public function fetchFormsSoldStatsByPurchaseStatus()
    {
        $query = "SELECT 
                    pd.status, COUNT(pd.id) AS total, SUM(ft.amount) AS amount 
                FROM 
                    purchase_detail AS pd, forms AS ft, 
                    admission_period AS ap, vendor_details AS v  
                WHERE
                    pd.form_id = ft.id AND pd.admission_period = ap.id AND 
                    pd.vendor = v.id AND ap.active = 1 
                GROUP BY pd.status";
        return $this->dm->getData($query);
    }

    /**
     * fetching applicants data
     */

    public function fetchAppsSummaryData($data)
    {
        // extract the array values into variables
        // create a new array with the keys of $data as the values and the values of $data as the keys
        // and then extract the values of the new array into variables
        extract(array_combine(array_keys($data), array_values($data)));

        $SQL_COND = "";
        if ($country != "All") $SQL_COND .= " AND p.`nationality` = '$country'";
        if ($type != "All") $SQL_COND .= " AND ft.`id` = $type";
        if ($program != "All") $SQL_COND .= " AND pi.`first_prog` = '$program' OR pi.`second_prog` = '$program'";

        $SQL_COND;

        $result = array();
        switch ($action) {
            case 'apps-total':
                $result = $this->fetchAllApplication($SQL_COND);
                break;
            case 'apps-submitted':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(true, $SQL_COND);
                break;

            case 'apps-in-progress':
                $result = $this->fetchAllSubmittedOrUnsubmittecApplication(false, $SQL_COND);
                break;

            case 'apps-admitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(true, $SQL_COND);
                break;

            case 'apps-unadmitted':
                $result = $this->fetchAllAdmittedOrUnAdmittedApplication(false, $SQL_COND);
                break;

            case 'apps-awaiting':
                $result = $this->fetchAllAwaitingApplication($SQL_COND);
                break;
        }
        return $result;
    }

    public function fetchAllApplication($SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    forms AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_id = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 $SQL_COND";
        return $this->dm->getData($query);
    }

    public function fetchAllSubmittedOrUnsubmittecApplication(bool $submitted, $SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    forms AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_id = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.declaration = :s $SQL_COND";
        return $this->dm->getData($query, array(":s" => (int) $submitted));
    }

    public function fetchAllAdmittedOrUnAdmittedApplication(bool $admitted, $SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    forms AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_id = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.admitted = :s $SQL_COND";
        return $this->dm->getData($query, array(":s" => (int) $admitted));
    }

    public function fetchAllAwaitingApplication($SQL_COND)
    {
        $query = "SELECT 
                    al.id, CONCAT(p.first_name, ' ', IFNULL(p.middle_name, ''), ' ', p.last_name) AS fullname, 
                    p.nationality, ft.name AS app_type, pi.first_prog, pi.second_prog, fs.declaration 
                FROM 
                    personal_information AS p, applicants_login AS al, 
                    forms AS ft, purchase_detail AS pd, program_info AS pi, 
                    form_sections_chek AS fs, admission_period AS ap, academic_background AS ab 
                WHERE 
                    p.app_login = al.id AND pi.app_login = al.id AND fs.app_login = al.id AND ab.app_login = al.id AND
                    pd.admission_period = ap.id AND pd.form_id = ft.id AND pd.id = al.purchase_id AND 
                    ap.active = 1 AND fs.declaration = 1 AND ab.awaiting_result = 1$SQL_COND";
        return $this->dm->getData($query);
    }

    public function fetchTotalApplications(int $form_id = 100)
    {
        if ($form_id == 100) {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_id = ft.id";
            return $this->dm->getData($query);
        } else {
            $query = "SELECT 
                    COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                    AND pd.form_id = ft.id AND ft.id = :f";
            return $this->dm->getData($query, array(":f" => $form_id));
        }
    }

    public function fetchTotalSubmittedOrUnsubmittedApps(int $form_id, bool $submitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM 
                    purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft
                WHERE 
                    ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id 
                AND pd.form_id = ft.id AND fc.declaration = :s AND ft.id = :f";
        return $this->dm->getData($query, array(":s" => (int) $submitted, ":f" => $form_id));
    }

    public function fetchTotalAdmittedOrUnadmittedApplicants(int $form_id, bool $admitted = true)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                pd.form_id = ft.id AND fc.`admitted` = :s AND ft.id = :f";
        return $this->dm->getData($query, array(":s" => (int) $admitted, ":f" => $form_id));
    }

    public function fetchTotalAwaitingResultsByFormType(int $form_id)
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_id = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1 AND ft.id = :f";
        return $this->dm->getData($query, array(":f" => $form_id));
    }

    public function fetchTotalAwaitingResults()
    {
        $query = "SELECT COUNT(*) AS total 
                FROM purchase_detail AS pd, admission_period AS ap, form_sections_chek AS fc, applicants_login AS al, forms AS ft, 
                academic_background AS ab 
                WHERE ap.id = pd.admission_period AND ap.active = 1 AND fc.app_login = al.id AND al.purchase_id = pd.id AND 
                ab.app_login = al.id AND pd.form_id = ft.id AND fc.`declaration` = 1 AND ab.`awaiting_result` = 1";
        return $this->dm->getData($query);
    }

    public function getAllAdmittedApplicantsAllAll($cert_type)
    {
        $in_query = "";
        if ($cert_type != "All") $in_query = "AND ab.cert_type = '$cert_type'";
        $query = "SELECT p.`first_name`, p.`middle_name`, p.`last_name`, fs.first_prog_qualified, fs.second_prog_qualified, 
                    pi.first_prog, pi.second_prog, pi.application_term, pi.study_stream, pd.`form_id`, a.id 
                FROM `personal_information` AS p, `applicants_login` AS a, form_sections_chek AS fs, 
                    academic_background AS ab, program_info AS pi, purchase_detail AS pd 
                WHERE p.app_login = a.id AND ab.app_login = a.id AND fs.app_login = a.id AND 
                    pd.id = a.purchase_id AND pi.app_login = a.id AND fs.admitted = 1 $in_query";
        return $this->dm->getData($query);
    }

    public function getAllDeclinedApplicantsAllAll($cert_type)
    {
        $in_query = "";
        if ($cert_type != "All") $in_query = "AND ab.cert_type = '$cert_type'";
        $query = "SELECT p.`first_name`, p.`middle_name`, p.`last_name`, fs.first_prog_qualified, fs.second_prog_qualified, 
                    pi.first_prog, pi.second_prog, pi.application_term, pi.study_stream, pd.`form_id`, a.id  
                FROM `personal_information` AS p, `applicants_login` AS a, form_sections_chek AS fs, 
                    academic_background AS ab, program_info AS pi, purchase_detail AS pd 
                WHERE p.app_login = a.id AND ab.app_login = a.id AND fs.app_login = a.id AND 
                    pd.id = a.purchase_id AND pi.app_login = a.id AND fs.declined = 1 $in_query";
        return $this->dm->getData($query);
    }

    public function getAllAdmitedApplicants($cert_type)
    {
        $in_query = "";
        if (in_array($cert_type, ["WASSCE", "NECO"])) $in_query = "AND ab.cert_type IN ('WASSCE', 'NECO')";
        if (in_array($cert_type, ["SSSCE", "GBCE"])) $in_query = "AND ab.cert_type IN ('SSSCE', 'GBCE')";
        if (in_array($cert_type, ["BACCALAUREATE"])) $in_query = "AND ab.cert_type IN ('BACCALAUREATE')";

        $query = "SELECT a.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, pg.name AS programme, b.program_choice 
                FROM `personal_information` AS p, `applicants_login` AS a, broadsheets AS b, programs AS pg,  academic_background AS ab  
                WHERE p.app_login = a.id AND b.app_login = a.id AND ab.app_login = a.id AND pg.id = b.program_id AND 
                a.id IN (SELECT b.app_login AS id FROM broadsheets AS b) $in_query";
        return $this->dm->getData($query);
    }

    public function getAllUnadmitedApplicants($certificate, $progCategory)
    {
        $query = "SELECT l.`id`, p.`first_name`, p.`middle_name`, p.`last_name`, 
                    p.`email_addr`, i.`$progCategory` AS programme, a.`cert_type` 
                FROM 
                    `personal_information` AS p, `academic_background` AS a, 
                    `applicants_login` AS l, `form_sections_chek` AS f, `program_info` AS i 
                WHERE 
                    p.`app_login` = l.`id` AND a.`app_login` = l.`id` AND 
                    f.`app_login` = l.`id` AND i.`app_login` = l.`id` AND
                    a.`awaiting_result` = 0 AND f.`declaration` = 1 AND 
                    f.`admitted` = 0 AND f.`declined` = 0";
        $param = array();
        if (strtolower($certificate) != "all") {
            $query .= " AND a.`cert_type` = :c";
            $param = array(":c" => $certificate);
        }

        return $this->dm->getData($query, $param);
    }

    public function getAppCourseSubjects(int $loginID)
    {
        $query = "SELECT 
                    r.`type`, r.`subject`, r.`grade` 
                FROM 
                    academic_background AS a, high_school_results AS r, applicants_login AS l
                WHERE 
                    l.`id` = a.`app_login` AND r.`acad_back_id` = a.`id` AND l.`id` = :i";
        return $this->dm->getData($query, array(":i" => $loginID));
    }

    public function getAppProgDetails($program)
    {
        $query = "SELECT `id`, `name` `type`, `group`, `weekend` FROM programs WHERE `name` = :p";
        return $this->dm->getData($query, array(":p" => $program));
    }

    public function bundleApplicantsData($data, $prog_category = "")
    {
        $store = [];
        foreach ($data as  $appData) {
            if ($prog_category == "") $prog_category = $appData["program_choice"];
            $applicant = [];
            $applicant["app_pers"] = $appData;
            $applicant["app_pers"]["prog_category"] = $prog_category;
            $subjs = $this->getAppCourseSubjects($appData["id"]);
            $applicant["sch_rslt"] = $subjs;
            $progs = $this->getAppProgDetails($appData["programme"]);
            $applicant["prog_info"] = $progs;
            array_push($store, $applicant);
        }
        return $store;
    }

    public function fetchAllUnadmittedApplicantsData($certificate, $progCategory)
    {
        $allAppData = $this->getAllUnadmitedApplicants($certificate, $progCategory);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData, $progCategory);
        return $store;
    }

    public function fetchAllAdmittedApplicantsData($cert_type)
    {
        $allAppData = $this->getAllAdmitedApplicants($cert_type);
        if (empty($allAppData)) return 0;

        $store = $this->bundleApplicantsData($allAppData);
        return $store;
    }

    public function saveAdmittedApplicantData(int $admin_period, int $appID, int $program_id, $admitted_data, $prog_choice)
    {
        if (empty($appID) || empty($admin_period) || empty($program_id) || empty($admitted_data)) return 0;

        $query = "INSERT INTO `broadsheets` (`admin_period`,`app_login`,`program_id`,
                `required_core_passed`,`any_one_core_passed`,`total_core_score`,`any_three_elective_passed`,
                `total_elective_score`,`total_score`,`program_choice`) 
                VALUES (:ap, :al, :pi, :rcp, :aocp, :tcs, :atep, :tes, :ts, :pc)";
        $params = array(
            ":ap" => $admin_period,
            ":al" => $appID,
            ":pi" => $program_id,
            ":rcp" => $admitted_data["required_core_passed"],
            ":aocp" => $admitted_data["any_one_core_passed"],
            ":tcs" => $admitted_data["total_core_score"],
            ":atep" => $admitted_data["any_three_elective_passed"],
            ":tes" => $admitted_data["total_elective_score"],
            ":ts" => $admitted_data["total_score"],
            ":pc" => $prog_choice
        );
        $this->dm->inputData($query, $params);
    }

    /*
    * Admit applicants in groups by their certificate category
    */
    public function admitApplicantsByCertCat($data, $qualifications)
    {
        $final_result = [];

        foreach ($data as $std_data) {
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["A"])) {
                array_push($final_result, $this->admitByCatA($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["B"])) {
                array_push($final_result, $this->admitByCatB($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["C"])) {
                array_push($final_result, $this->admitByCatC($std_data));
                continue;
            }
            if (in_array($std_data["app_pers"]["cert_type"], $qualifications["D"])) {
                array_push($final_result, $this->admitByCatD($std_data));
                continue;
            }
        }
        return $final_result;
    }

    public function admitCatAApplicant($app_result, $prog_choice, $cert_type)
    {

        $qualified = false;
        // Admit applicant
        if ($app_result["feed"]["required_core_passed"] == 2 && $app_result["feed"]["any_one_core_passed"] > 0 && $app_result["feed"]["any_three_elective_passed"] >= 3) {

            if (in_array($cert_type, ["SSSCE", "NECO", "GBCE"]) && $app_result["feed"]["total_score"] <= 24) {
                $qualified = true;
            }

            if (in_array($cert_type, ["WASSCE"]) && $app_result["feed"]["total_score"] <= 36) {
                $qualified = true;
            }

            if ($qualified) {
                $query = "UPDATE `form_sections_chek` SET `admitted` = 1, `declined` = 0, `$prog_choice` = 1 WHERE `app_login` = :i";
                $this->dm->getData($query, array(":i" => $app_result["id"]));
                return $qualified;
            }
        } else {
            $query = "UPDATE `form_sections_chek` SET `admitted` = 0, `declined` = 1,  `$prog_choice` = 1 WHERE `app_login` = :i";
            $this->dm->getData($query, array(":i" => $app_result["id"]));
            return $qualified;
        }
    }

    private function admitWASSCELike($data)
    {
    }

    public function admitByCatA($data)
    {
        foreach ($data["sch_rslt"] as $result) {
            die(json_encode($result));
        }

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        $total_core_score = 0;
        $required_core_passed = 0;
        $any_one_core_passed = 0;
        $any_one_core_score = 7;

        $any_three_elective_passed = 0;
        $total_elective_score = 0;
        $any_three_elective_scores = [];

        foreach ($data["sch_rslt"] as $result) {

            $score = 7;
            for ($i = 0; $i < count($grade_range); $i++) {
                if ($result["grade"] == $grade_range[$i]["grade"]) {
                    $score = $grade_range[$i]['score'];
                }
            }

            if ($score >= 1 && $score <= 6) {
                if (strtolower($result["type"]) == "core") {
                    if (strtolower($result["subject"]) == "core mathematics" || strtolower($result["subject"]) == "english language") {
                        $required_core_passed += 1;
                        $total_core_score += $score;
                    } else {

                        if (!empty($any_one_core_passed)) {
                            $total_core_score -= $any_one_core_score;
                        }
                        if (empty($any_one_core_passed)) {
                            $any_one_core_score = $score;
                        }
                        $any_one_core_passed += 1;
                        $total_core_score += $score;
                    }
                }

                if (strtolower($result["type"]) == "elective") {
                    $any_three_elective_passed += 1;
                    array_push($any_three_elective_scores, $score);
                }
            }

            /*die(json_encode(
                array(
                    "total_core_score" => $total_core_score,
                    "required_core_passed" => $required_core_passed,
                    "any_one_core_passed" => $any_one_core_passed,
                    "any_one_core_score" => $any_one_core_score,
                    "any_three_elective_passed" => $any_three_elective_passed,
                    "total_elective_score" => $total_elective_score,
                    "any_three_elective_scores" => $any_three_elective_scores,
                )
            ));*/
        }

        $array_before_sort = $any_three_elective_scores;
        asort($any_three_elective_scores);
        $array_with_new_values = array_values($any_three_elective_scores);
        $any_three_elective_scores = array_values($any_three_elective_scores);
        if (count($any_three_elective_scores) > 3) unset($any_three_elective_scores[count($any_three_elective_scores) - 1]);
        $total_elective_score = array_sum($any_three_elective_scores);

        $feed["total_core_score"] = $total_core_score;
        $feed["total_elective_score"] = $total_elective_score;
        $feed["total_score"] = $total_core_score + $total_elective_score;
        $feed["required_core_passed"] = $required_core_passed;
        $feed["any_one_core_passed"] = $any_one_core_passed;
        $feed["any_one_core_score"] = $any_one_core_score;
        $feed["any_three_elective_passed"] = $any_three_elective_passed;
        $feed["any_three_elective_scores"] = $any_three_elective_scores;
        $feed["array_before_sort"] = $array_before_sort;
        $feed["array_with_new_values"] = $array_with_new_values;

        $app_result["id"] = $data["app_pers"]["id"];
        $app_result["feed"] = $feed;
        $app_result["admitted"] = false;
        //$app_result["emailed"] = false;

        $prog_choice = $data["app_pers"]["prog_category"] . "_qualified";

        $app_result["admitted"] = $this->admitCatAApplicant($app_result, $prog_choice, $data["app_pers"]["cert_type"]);
        $admin_period = $this->getCurrentAdmissionPeriodID();

        if ($app_result["admitted"]) {
            $this->saveAdmittedApplicantData($admin_period, $app_result["id"], $data["prog_info"][0]["id"], $app_result["feed"], $data["app_pers"]["prog_category"]);
        }

        $this->logActivity(
            $_SESSION["user"],
            "INSERT",
            "Admitted applicants {$app_result["id"]} through mass admit with following: 
            admission status(addtitted): {$app_result["admitted"]}, admission period = {$admin_period}, 
            program id: {$data["prog_info"][0]["id"]}, program category: {$data["app_pers"]["prog_category"]}"
        );

        return $app_result;
    }

    public function admitByCatB($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitByCatC($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitByCatD($bs_data)
    {
        $final_result = [];

        // set all qualified grades
        $grade_range = array(
            array('grade' => 'A1', 'score' => 1),
            array('grade' => 'B2', 'score' => 2),
            array('grade' => 'B3', 'score' => 3),
            array('grade' => 'C4', 'score' => 4),
            array('grade' => 'C5', 'score'  => 5),
            array('grade' => 'C6', 'score'  => 6),
            array('grade' => 'A', 'score' => 1),
            array('grade' => 'B', 'score' => 2),
            array('grade' => 'C', 'score' => 3),
            array('grade' => 'D', 'score' => 4)
        );

        return $final_result;
    }

    public function admitQualifiedStudents($certificate, $progCategory)
    {
        $students_bs_data = $this->fetchAllUnadmittedApplicantsData($certificate, $progCategory);
        die(json_encode($students_bs_data));
        if (!empty($students_bs_data)) {
            $qualifications = array(
                "A" => array('WASSCE', 'SSSCE', 'GBCE', 'NECO'),
                "B" => array('GCE', "GCE 'A' Level", "GCE 'O' Level"),
                "C" => array('HND'),
                "D" => array('IB', 'International Baccalaureate', 'Baccalaureate'),
            );

            return $this->admitApplicantsByCertCat($students_bs_data, $qualifications);
        }

        return 0;
    }

    private function getAppProgDetailsByAppID($appID)
    {
        $sql = "SELECT * FROM `program_info` WHERE `app_login` = :i";
        return $this->dm->getData($sql, array(':i' => $appID));
    }

    private function getApplicantContactInfo($appID)
    {
        $sql = "SELECT * FROM `personal_information` WHERE `app_login` = :i";
        return $this->dm->getData($sql, array(':i' => $appID));
    }

    public function admitIndividualApplicant($appID, $prog_choice)
    {
        $prog_choice_q = $prog_choice . "_qualified";
        $query = "UPDATE `form_sections_chek` SET `admitted` = 1, `declined` = 0, `$prog_choice_q` = 1 WHERE `app_login` = :i";
        if ($this->dm->inputData($query, array(":i" => $appID))) {
            $contactInfo = $this->getApplicantContactInfo($appID);
            $programInfo = $this->getAppProgDetailsByAppID($appID);

            // Prepare SMS message
            $message = 'Hi ' . ucfirst(strtolower($contactInfo[0]["first_name"])) . " " . ucfirst(strtolower($contactInfo[0]["last_name"])) . '. ';
            $message .= 'Congratulations! You have been offered admission into Regional Maritime University to read ' . $programInfo[0][$prog_choice];
            $message .= ' as a ' . $programInfo[0]['study_stream'] . " student. To secure this offer, please ";
            $message .= 'visit the application portal at https://admissions.rmuictonline.com and login to complete an acceptance form.';
            $to = $contactInfo[0]["phone_no1_code"] . $contactInfo[0]["phone_no1"];

            $sentEmail = false;
            $smsSent = false;

            // Send SMS message
            $response = json_decode($this->expose->sendSMS($to, $message));

            // Set SMS response status
            if (!$response->status) $smsSent = true;

            // Check if email address was provided
            if (!empty($data[0]["email_address"])) {
                // Prepare email message
                $e_message = '<p>Hi ' . $data[0]["first_name"] . ",</p>";
                $e_message .= '<p>Congratulations! You have been offered admission into Regional Maritime University to read ' . $programInfo[0][$prog_choice];
                $e_message .= 'as a ' . strtolower($programInfo[0]['study_stream']) . ' student.</p>';
                $e_message .= '<p>To secure this offer, please visit the application portal at https://admissions.rmuictonline.com and login to complete an acceptance form.';

                // Send email message
                $e_response = $this->expose->sendEmail($contactInfo[0]["email_addr"], 'ONLINE APPLICATION PORTAL LOGIN INFORMATION', $e_message);

                // Ste email reponse status
                if ($e_response) $sentEmail = true;
            }

            // Set output message
            $output = "";
            if ($smsSent || $sentEmail) $output = "Applicant admitted successfully and SMS/email sent!";
            else $output = "Applicant admitted successfully but failed to send SMS/Email!";

            // Log activity
            $this->logActivity(
                $_SESSION["user"],
                "INSERT",
                "Admissions user {$_SESSION["user"]} admitted applicant with ID {$appID}"
            );

            // return output message
            return array("success" => true, "message" => $output);
        }
        return array("success" => false, "message" => "Failed to admit applicant!");
    }

    public function declineIndividualApplicant($appID)
    {
        $query = "UPDATE `form_sections_chek` SET `admitted` = 0, `declined` = 1  WHERE `app_login` = :i";
        if ($this->dm->inputData($query, array(":i" => $appID))) {
            return array("success" => true, "message" => "Succesfully declined applicant admission!");
        }
        return array("success" => false, "message" => "Failed to declined applicant admission!");
    }

    public function updateApplicationStatus($appID)
    {
        $query = "UPDATE `form_sections_chek` SET `reviewed` = 1 WHERE `app_login` = :i";
        $this->dm->inputData($query, array(":i" => $appID));
    }

    /**
     * For accounts officers
     */

    // fetch dashboards stats for a vendor 
    public function fetchVendorSummary(int $vendor_id)
    {
        $result["form-types"] = array();
        $allAvailableForms = $this->dm->getData("SELECT * FROM forms");
        foreach ($allAvailableForms as $form) {
            $form_id = $form['id'];
            $query = "SELECT ft.name, COUNT(*) AS total_num, SUM(pd.amount) AS total_amount, pd.amount AS unit_price 
                FROM purchase_detail AS pd, admission_period AS ap, forms AS ft, vendor_details AS vd 
                WHERE pd.admission_period = ap.id AND ap.active = 1 AND pd.form_id = ft.id AND pd.vendor = vd.id 
                AND pd.status = 'COMPLETED' AND ft.id = {$form_id} AND vd.id = {$vendor_id}";
            array_push($result["form-types"], $this->dm->getData($query)[0]);
        }
        return $result;
    }

    // fetch dashboards stats
    public function fetchInitialSummaryRecord()
    {
        $result = array();
        $result["transactions"] = [];
        $result["collections"] = [];
        $result["form-types"] = [];

        $transaction_statuses = ["TOTAL", "COMPLETED", "PENDING", "FAILED"];

        $i = 1;
        foreach ($transaction_statuses as $status) {
            if ($i == 1)
                $query = "SELECT COUNT(*) AS total FROM purchase_detail AS pd, admission_period AS ap 
                        WHERE pd.admission_period = ap.id AND ap.active = 1";
            else
                $query = "SELECT pd.status, COUNT(*) AS total FROM purchase_detail AS pd, admission_period AS ap 
                WHERE pd.admission_period = ap.id AND ap.active = 1 AND pd.status = '{$status}'";
            array_push($result["transactions"], $this->dm->getData($query)[0]);
            $i += 1;
        }

        $query5 = "SELECT COUNT(*) AS total_num, SUM(pd.amount) AS total_amount 
                FROM purchase_detail AS pd, admission_period AS ap 
                WHERE pd.admission_period = ap.id AND ap.active = 1 AND pd.status = 'COMPLETED'";
        $query6 = "SELECT COUNT(*) AS total_num, SUM(pd.amount) AS total_amount 
                FROM purchase_detail AS pd, admission_period AS ap, vendor_details AS vd  
                WHERE pd.admission_period = ap.id AND pd.vendor = vd.id AND vd.type <> 'ONLINE' AND ap.active = 1 AND pd.status = 'COMPLETED'";
        $query7 = "SELECT COUNT(*) AS total_num, SUM(pd.amount) AS total_amount 
                FROM purchase_detail AS pd, admission_period AS ap, vendor_details AS vd  
                WHERE pd.admission_period = ap.id AND pd.vendor = vd.id AND vd.type = 'ONLINE' AND ap.active = 1 AND pd.status = 'COMPLETED'";
        $query8 = "SELECT COUNT(*) AS total_num, SUM(pd.amount) AS total_amount 
                FROM purchase_detail AS pd, admission_period AS ap, vendor_details AS vd  
                WHERE pd.admission_period = ap.id AND pd.vendor = vd.id AND vd.type = 'ONLINE' AND ap.active = 1 AND pd.status = 'COMPLETED'";

        $result["collections"]["collect"] = $this->dm->getData($query5)[0];
        $result["collections"]["vendor"] = $this->dm->getData($query6)[0];
        $result["collections"]["online"] = $this->dm->getData($query7)[0];
        $result["collections"]["provider"] = $this->dm->getData($query8)[0];

        $allAvailableForms = $this->dm->getData("SELECT * FROM forms");
        foreach ($allAvailableForms as $form) {
            $form_id = $form['id'];
            $query9 = "SELECT ft.name, COUNT(*) AS total_num, SUM(pd.amount) AS total_amount, pd.amount AS unit_price 
                FROM purchase_detail AS pd, admission_period AS ap, forms AS ft 
                WHERE pd.admission_period = ap.id AND ap.active = 1 AND pd.form_id = ft.id 
                AND pd.status = 'COMPLETED' AND ft.id = '{$form_id}'";
            array_push($result["form-types"], $this->dm->getData($query9)[0]);
        }

        return $result;
    }

    public function fetchAllFormPurchases($data = array())
    {
        $QUERY_CON = "";
        if (strtolower($data["admission-period"]) != "all" && !empty($data["admission-period"]))
            $QUERY_CON .= " AND pd.`admission_period` = '" . $data["admission-period"] . "'";
        if (!empty($data["from-date"])  && !empty($data["to-date"]))
            $QUERY_CON .= " AND pd.`added_at` BETWEEN '" . $data["from-date"] . "'" . " AND '" . $data["to-date"] . "'";
        if (strtolower($data["form-type"]) != "all" && !empty($data["form-type"]))
            $QUERY_CON .= " AND pd.`form_id` = '" . $data["form-type"] . "'";
        if (strtolower($data["purchase-status"]) != "all" && !empty($data["purchase-status"]))
            $QUERY_CON .= " AND pd.`status` = '" . $data["purchase-status"] . "'";
        if (strtolower($data["payment-method"]) != "all" && !empty($data["payment-method"]))
            $QUERY_CON .= " AND pd.`payment_method` = '" . $data["payment-method"] . "'";

        $query = "SELECT pd.`id`, CONCAT(pd.`first_name`, ' ', pd.`last_name`) AS fullName, 
                 CONCAT('(', pd.`country_code`,') ', pd.`phone_number`) AS phoneNumber, 
                 pd.`status`, pd.`added_at`, ft.`name` AS formType, ap.`info` AS admissionPeriod, pd.`payment_method` AS paymentMethod 
                 FROM `purchase_detail` AS pd, `admission_period` AS ap, `forms` AS ft, vendor_details AS vd 
                 WHERE pd.admission_period = ap.`id` AND pd.form_id = ft.id AND pd.vendor = vd.`id`$QUERY_CON";
        return $this->dm->getData($query);
    }

    public function fetchFormPurchaseDetailsByTranID(int $transID)
    {
        $query = "SELECT pd.`id` AS transID, CONCAT(pd.`first_name`, ' ', pd.`last_name`) AS fullName, 
                pd.`email_address` AS email,  CONCAT('(', pd.`country_code`,') ', pd.`phone_number`) AS phoneN, 
                pd.`country_name` AS country, pd.`app_number` AS appN, pd.`pin_number` AS pin, 
                pd.`status`, pd.`added_at`, ft.`name` AS formT, pd.`payment_method` AS payM, 
                vd.`company` AS vendor, ap.`info` AS admisP 
                FROM `purchase_detail` AS pd, `admission_period` AS ap, `forms` AS ft, vendor_details AS vd 
                WHERE pd.`admission_period` = ap.`id` AND pd.`form_id` = ft.`id` AND pd.`vendor` = vd.`id` AND pd.`id` = :ti";
        return $this->dm->getData($query, array(":ti" => $transID));
    }

    // Proccesses to generate and send a new applicant login details
    private function genPin(int $length_pin = 9)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str_result), 0, $length_pin);
    }

    private function genAppNumber(int $type, int $year)
    {
        $user_code = $this->expose->genCode(5);
        $app_number = ($type * 10000000) + ($year * 100000) + $user_code;
        return $app_number;
    }

    private function doesCodeExists($code)
    {
        $sql = "SELECT `id` FROM `applicants_login` WHERE `app_number`=:p";
        if ($this->dm->getID($sql, array(':p' => sha1($code)))) {
            return 1;
        }
        return 0;
    }

    private function getAppPurchaseData(int $trans_id)
    {
        $sql = "SELECT f.`form_id`, pd.`country_code`, pd.`phone_number`, pd.`email_address` 
                FROM `purchase_detail` AS pd, forms AS f WHERE pd.`id` = :t AND f.`id` = pd.`form_id`";
        return $this->dm->getData($sql, array(':t' => $trans_id));
    }

    private function genLoginDetails(int $type, int $year)
    {
        $rslt = 1;
        while ($rslt) {
            $app_num = $this->genAppNumber($type, $year);
            $rslt = $this->doesCodeExists($app_num);
        }
        $pin = strtoupper($this->genPin());
        return array('app_number' => $app_num, 'pin_number' => $pin);
    }

    private function updateVendorPurchaseData(int $trans_id, int $app_number, $pin_number, $status)
    {
        $sql = "UPDATE `purchase_detail` SET `app_number`= :a,`pin_number`= :p, `status` = :s WHERE `id` = :t";
        return $this->dm->getData($sql, array(':a' => $app_number, ':p' => $pin_number, ':s' => $status, ':t' => $trans_id));
    }

    private function genLoginsAndSend(int $trans_id)
    {
        $data = $this->getAppPurchaseData($trans_id);

        if (empty($data)) return array("success" => false, "message" => "No data records for this transaction!");

        $app_type = 0;

        if ($data[0]["form_id"] >= 2) {
            $app_type = 1;
        } else if ($data[0]["form_id"] == 1) {
            $app_type = 2;
        }

        $app_year = $this->expose->getAdminYearCode();

        $login_details = $this->genLoginDetails($app_type, $app_year);
        if ($this->updateVendorPurchaseData($trans_id, $login_details['app_number'], $login_details['pin_number'], 'COMPLETED'))
            return array("success" => true, "message" => $login_details);
        return array("success" => false, "message" => "Failed to update purchase records!");
    }

    /**
     * Generates and sends new applicant login details
     * @param transID - transaction id of purchase 
     */
    public function sendPurchaseInfo(int $transID)
    {
        //geenerate new login details
        $gen = $this->genLoginsAndSend($transID);
        if (!$gen["success"]) return $gen;

        // Get purchase data
        $data = $this->dm->getData("SELECT * FROM purchase_detail WHERE id = :ti", array(":ti" => $transID));
        if (empty($data)) return array("success" => false, "message" => "No data foound for this transaction!");

        // Prepare SMS message
        $message = 'Your RMU Online Application login details. ';
        $message .= 'APPLICATION NUMBER: RMU-' . $data[0]['app_number'];
        $message .= '    PIN: ' . $data[0]['pin_number'] . ".";
        $message .= ' Follow the link, https://admissions.rmuictonline.com start application process.';
        $to = $data[0]["country_code"] . $data[0]["phone_number"];

        $sentEmail = false;
        $smsSent = false;

        // Send SMS message
        $response = json_decode($this->expose->sendSMS($to, $message));

        // Set SMS response status
        if (!$response->status) {
            $smsSent = true;
        }

        // Check if email address was provided
        if (!empty($data[0]["email_address"])) {

            // Prepare email message
            $e_message = '<p>Hi ' . $data[0]["first_name"] . ",</p>";
            $e_message .= '<p>Your RMU Online Application login details. </p>';
            $e_message .= '<p>APPLICATION NUMBER: RMU-' . $data[0]['app_number'] . '</p>';
            $e_message .= '<p>PIN: ' . $data[0]['pin_number'] . "</p>";
            $e_message .= '<p>Follow the link, https://admissions.rmuictonline.com to start application process.</p>';

            // Send email message
            $e_response = $this->expose->sendEmail($data[0]["email_address"], 'ONLINE APPLICATION PORTAL LOGIN INFORMATION', $e_message);

            // Ste email reponse status
            if ($e_response) {
                $sentEmail = true;
            }
        }

        // Set output message
        $output = "";
        if ($smsSent && $sentEmail) $output = "Successfully, sent purchase details via SMS and email!";
        else $output = "Successfully, sent purchase details!";

        // Log activity
        $this->logActivity(
            $_SESSION["user"],
            "INSERT",
            "Account user {$_SESSION["user"]} sent purchase details with transaction ID {$transID}"
        );

        // return output message
        return array("success" => true, "message" => $output);
    }

    public function prepareDownloadQuery($data)
    {
        $QUERY_CON = "";
        if (strtolower($data["admission-period"]) != "all" && !empty($data["admission-period"]))
            $QUERY_CON .= " AND pd.`admission_period` = '" . $data["admission-period"] . "'";
        if (!empty($data["from-date"])  && !empty($data["to-date"]))
            $QUERY_CON .= " AND pd.`added_at` BETWEEN '" . $data["from-date"] . "' AND '" . $data["to-date"] . "'";
        if (strtolower($data["form-type"]) != "all" && !empty($data["form-type"]))
            $QUERY_CON .= " AND pd.`form_id` = '" . $data["form-type"] . "'";
        if (strtolower($data["purchase-status"]) != "all" && !empty($data["purchase-status"]))
            $QUERY_CON .= " AND pd.`status` = '" . $data["purchase-status"] . "'";
        if (strtolower($data["payment-method"]) != "all" && !empty($data["payment-method"]))
            $QUERY_CON .= " AND pd.`payment_method` = '" . $data["payment-method"] . "'";

        $_SESSION["downloadQuery"] = "SELECT pd.`id`, CONCAT(pd.`first_name`, ' ', pd.`last_name`) AS fullName, 
                 CONCAT('(', pd.`country_code`,') ', pd.`phone_number`) AS phoneNumber, 
                 pd.`status`, pd.`added_at`, ft.`name` AS formType, ap.`info` AS admissionPeriod, pd.`payment_method` AS paymentMethod 
                 FROM `purchase_detail` AS pd, `admission_period` AS ap, `forms` AS ft, vendor_details AS vd 
                 WHERE pd.admission_period = ap.`id` AND pd.form_id = ft.id AND pd.vendor = vd.`id`$QUERY_CON";
        if (isset($_SESSION["downloadQuery"]) && !empty($_SESSION["downloadQuery"])) return 1;
        return 0;
    }

    public function executeDownloadQuery()
    {
        return $this->dm->getData($_SESSION["downloadQuery"]);
    }

    public function fetchFormPurchasesGroupReport($data)
    {
        $query = "";
        if ($data == "PayMethod") {
            $query = "SELECT pm.id, pd.payment_method AS title, COUNT(pd.payment_method) AS total_num_sold, SUM(pd.amount) AS total_amount_sold
                    FROM purchase_detail AS pd, vendor_details AS vd, admission_period AS ap, forms AS ft, payment_method AS pm   
                    WHERE pd.admission_period = ap.id AND pd.vendor = vd.id AND pd.form_id = ft.id AND pd.payment_method = pm.name 
                    AND pd.`status` = 'COMPLETED' GROUP BY pd.payment_method";
        }
        if ($data == "Vendors") {
            $query = "SELECT vd.id, vd.company AS title, COUNT(pd.vendor) AS total_num_sold, SUM(pd.amount) AS total_amount_sold
                    FROM purchase_detail AS pd, vendor_details AS vd, admission_period AS ap, forms AS ft, payment_method AS pm 
                    WHERE pd.admission_period = ap.id AND pd.vendor = vd.id AND pd.form_id = ft.id AND pd.payment_method = pm.name 
                    AND pd.`status` = 'COMPLETED' GROUP BY pd.vendor";
        }
        return $this->dm->getData($query);
    }

    public function fetchFormPurchasesGroupReportInfo(int $i, $t)
    {
        $query = "";
        if ($t == "PayMethod") {
            $query = "SELECT * FROM purchase_detail AS pd, payment_method AS pm 
                    WHERE pd.payment_method = pm.name AND pm.id = :i AND pd.`status` = 'COMPLETED'";
        }
        if ($t == "Vendors") {
            $query = "SELECT * FROM purchase_detail AS pd, vendor_details AS vd 
                    WHERE pd.vendor = vd.id AND vd.id = :i AND pd.`status` = 'COMPLETED'";
        }
        return $this->dm->getData($query, array(":i" => $i));
    }

    // Excel Sheet Download for Admissions
    public function exportAdmissionData($status, $query)
    {
        $in_query = "";
        if ($status == "apps-completed") $in_query = "AND fsc.declaration = 1";
        if ($status == "apps-admitted") $in_query = "AND fsc.admitted = 1";
        if ($status == "apps-declined") $in_query = "AND fsc.declined = 1";
        if ($status == "apps-declined") $in_query = "AND fsc.declaration = 1";
        $sql = $query . " " . $in_query;
        return $this->dm->getData($sql);
    }

    public function downloadFile($file_url)
    {
        header('Content-Type:application/octet-stream');
        header("Content-Transfer-Encoding:utf-8");
        header("Content-disposition:attachment;filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
    }
}
