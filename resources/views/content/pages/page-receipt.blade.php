<?php
// include 'includes/config.inc.php';
//  include 'header.php' ;


// if (!$_SESSION['loggedIn']) {
//     $_SESSION['loginmsg'] = 'Kindly Signin or Signup to continue';
//     echo"<script>window.location.href='signin'</script>";
// }
?>


        <!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required Fremwork
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">-->
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{ asset("css/bootstrap.css") }}">
    <link rel="stylesheet" type="text/css" href="{{ asset("css/receipt-style.css") }}">
    <!-- Favicon -->

</head>
<style type="text/css">
    body {
        font-family: 'Rajdhani', sans-serif;
    }

    .logo {
        height: 60px;
        width: 80px;
        margin-left: 41%;
        background-repeat: no-repeat;
        background-size: 70px 70px;
    }

    h6 {
        font-weight: 600;
        margin-top: 5px;
    }

    h4 {
        font-size: 18px;
        font-weight: 700;
        margin-top: 5px;
    }

    .t {
        font-size: 12px;
        margin-top: 5px;
        font-weight: 300;
    }

    h1 {
        font-size: 35px;
        font-weight: 700;
        text-shadow: 0 0 3px green, 0 0 5px #3333;
    }

    b {
        font-weight: 600;
    }

    th {
    //padding: 7px;
        font-weight: 700;
    }

    td {
        border-bottom: 1px solid #EEE;
        width: 50mm;
    }

    .paid {
        width: 100%;
        position: absolute;
        top: -20px;
        font-weight: 700;
        left: 0;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        z-index: 1;
        font-size: 20px;
        text-transform: uppercase;
        text-align: center;
        padding: 5px 0 3px 0;
        background: green;
        color: #fff;
        opacity: .5;
    }
</style>

<body>

<div id="print-content" class="login-block">
    <!-- Container-fluid starts -->
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php
// $id = $_SESSION['id'];
// $query = "SELECT * FROM students WHERE Student_id = '$id'";
// $result = mysqli_query($conn,$query);
// $row = mysqli_fetch_assoc($result);
// $student_name = $row['Fname']." ".$row['Lname'];
// $number = $row['Mob_no'];
// $email= $row['Email'];
// $hostel_id = $row['Hostel_id'];
// $room_id = $row['Room_id'];
// $refno = $row['Ref_no'];

// if (!$hostel_id) {
//     echo "<script>window.location.href='error'</script>";
// }
// else{
// //getting hostel name and room number
// $query_prfl = "SELECT * FROM hostel Where Hostel_id = '$hostel_id'";
// $result_prfl = mysqli_query($conn,$query_prfl);
// $row_prfl = mysqli_fetch_assoc($result_prfl);
// $hostel_name = $row_prfl['Hostel_name'];
// }

// if (!$room_id) {
//     echo "<script>window.location.href='error'</script>";
// }
// else{
// $query_prfl2 = "SELECT * FROM room Where Room_id = '$room_id' and Hostel_id='$hostel_id'";
// $result_prfl2 = mysqli_query($conn,$query_prfl2);
// $row_prfl2 = mysqli_fetch_assoc($result_prfl2);
// $room_no = $row_prfl2['Room_no'];

// $room_type = $row_prfl2['Room_type'];
// $date = $row_prfl2['Day_reserved'];
// }
// $manager_query = mysqli_query($conn, "select * from hostel_manager where Hostel_id=$hostel_id");
// $row = mysqli_fetch_assoc($manager_query);

// $cquery = mysqli_query($conn, "select * from company where Id= '1'");
// $crow = mysqli_fetch_assoc($cquery);
// $price = $row_prfl2['Price'] + $crow['Profit'];
                ?>

                <div class="auth-box card">
                    <div class="card-block">
                        <div class="row m-b-20">
                            <div class="col-md-12">
                                <span class="paid">Paid</span><br>
                                <div class="logo">
                                    <img class="img-fluid" src="{{ asset("images/logo/prvt-hostels.png") }}">
                                </div>
                                <h3 class="text-center"><?php // echo $crow['Company_name'] ?> Platform</h3>
                                <p class="text-center"><?php // echo $crow['Slogan'] ?><br>
                                    IT Department,TaTU <br>
                                    <?php // echo $crow['Mob_no1'] ?>/<?php // echo $crow['Mob_no2'] ?> <br>
                                    <?php // echo $crow['Email'] ?>
                                </p>
                                <br>
                                <p class="text-left">Date/Time : <?php // echo $date; ?></p>
                                <hr>

                                <div id="mid">
                                    <div class="col-md-12">
                                        <h4>Student Details</h4>
                                        <ul>
                                            <li><b>Bookings No. :</b> <?php // echo $refno; ?></li><br>
                                            <li><b>Student Name :</b> <?php // echo $student_name; ?></li><br>
                                            <li><b>Mobile No :</b> <?php // echo $number; ?></li><br>
                                            <li><b>Hostel Name:</b> <?php // echo $hostel_name; ?></li><br>
                                            <li><b>Room Number:</b> <?php // echo $room_no; ?></li><br>
                                        </ul>
                                    </div>
                                    <hr>
                                </div>
                                <h4 class="mt-3">Booking Details</h4>
                                <table class="mt-2 col-md-12">
                                    <thead>
                                    <tr class="m-b-20">
                                        <th>Hostel</th>
                                        <th>Room Type</th>
                                        <th>Price</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr class="mb-5">
                                        <td><?php // echo $hostel_name; ?></td>
                                        <td><?php // echo $room_type; ?></td>
                                        <td>GH&#8373;<?php // echo $price; ?>.00</td>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <h6>Grand Total:</h6>
                                        </td>
                                        <td>GH&#8373;<?php // echo $price; ?>.00</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <h6>Amount to Pay:</h6>
                                        </td>
                                        <td>GH&#8373;<?php // echo $price; ?>.00</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <h6>Status:</h6>
                                        </td>
                                        <td>Paid</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-inverse text-center m-b-0">Thank you for booking.</h6>
                                <p class="text-primary text-left"><a href="index"><b>Back to website</b></a></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end of form -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
        <div id="print-btn" class="btn btn-primary btn-sm mt-3" onclick="window.print();">Save or Print</div>
    </div>
    <!-- end of container-fluid -->
</div>


</body>

</html>
