<?php 
include('/LPU/xampp/htdocs/Project/db_connection/db.php');
session_start();
if (!$conn) {
    die("Database connection failed:" . $conn->connect_error);
}

// Check if the user is logged in as 'admin' or 'donor'
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'donor')) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_save_changes'])) {
    $req_id = $_POST['request_id'];
    $new_contact = $_POST['contact'];
    $new_hospital = $_POST['hospital_name'];
    $new_unit = (int) $_POST['blood_units']; // Ensure it's an integer

    // Fetch the request details from the database
    $getRequest = $conn->prepare("SELECT * FROM requests WHERE request_id = ?");
    $getRequest->bind_param("i", $req_id);
    $getRequest->execute();
    $result = $getRequest->get_result();

    if ($request = $result->fetch_assoc()) {
        $blood_group = $request['blood_group_requested'];
        $request_status = $request['request_status'];

        if (!empty($new_contact) && !empty($new_hospital) && !empty($new_unit)) {
            $conn->begin_transaction();
            try {
                // Check if there is enough blood stock available
                $stock_check_query = $conn->prepare("SELECT units_available FROM blood_stock WHERE blood_group = ? FOR UPDATE");
                $stock_check_query->bind_param("s", $blood_group);
                $stock_check_query->execute();
                $stock_result = $stock_check_query->get_result();

                if ($stock = $stock_result->fetch_assoc()) {
                    $available_units = (int) $stock['units_available'];

                    // Check if requested units are available
                    if ($available_units >= $new_unit) {
                        // Step 2: Proceed with updating the request
                        $req_update_query = $conn->prepare("UPDATE requests SET contact = ?, hospital_name = ?, units_requested = ? WHERE request_id = ?");
                        $req_update_query->bind_param("ssii", $new_contact, $new_hospital, $new_unit, $req_id);
                        $req_update_query->execute();

                        // If the request status is 'fulfilled', update the stock
                        if ($request_status === 'fulfilled') {
                            $new_stock_units = $available_units - $new_unit;

                            $stock_update_query = $conn->prepare("UPDATE blood_stock SET units_available = ? WHERE blood_group = ?");
                            $stock_update_query->bind_param("is", $new_stock_units, $blood_group);
                            $stock_update_query->execute();
                        }

                        $conn->commit();
                        echo "Changes saved and stock updated!";
                    } else {
                        // Not enough stock available
                        echo "Not enough units of blood group $blood_group available in stock.";
                    }
                } else {
                    // Blood group not found in stock
                    echo "No stock available for blood group $blood_group.";
                }
            } catch (Exception $e) {
                $conn->rollback();
                echo "Transaction failed: " . $e->getMessage();
            }
        } else {
            echo "Please fill in all required fields.";
        }
    } else {
        echo "Request not found.";
    }
}
?>
