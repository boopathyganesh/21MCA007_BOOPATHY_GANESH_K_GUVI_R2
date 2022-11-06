<?php 

if(isset($_POST['fname']) && 
   isset($_POST['uname']) &&  
   isset($_POST['pass']) &&
   isset($_POST['email']) &&
   isset($_POST['city'])
   ){

    include "db_conn.php";

    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $city = $_POST['city'];

    $data = "fname=".$fname."&uname=".$uname."&email=".$email;
    
    if (empty($fname)) {
    	$em = "Full name is required";
    	header("Location: ../register.php?error=$em&$data");
	    exit;
    }else if(empty($uname)){
    	$em = "User name is required";
    	header("Location: ../register.php?error=$em&$data");
	    exit;
    }else if(empty($pass)){
    	$em = "Password is required";
    	header("Location: ../register.php?error=$em&$data");
	    exit;
    }
    if (empty($email)) {
      $em = "E-Mail is required";
      header("Location: ../register.php?error=$em&$data");
      exit;
   }
    else {
      // hashing the password
      $pass = password_hash($pass, PASSWORD_DEFAULT);

      if (isset($_FILES['pp']['name']) AND !empty($_FILES['pp']['name'])) {
         
         
         $img_name = $_FILES['pp']['name'];
         $tmp_name = $_FILES['pp']['tmp_name'];
         $error = $_FILES['pp']['error'];
         
         if($error === 0){
            $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
            $img_ex_to_lc = strtolower($img_ex);

            $allowed_exs = array('jpg', 'jpeg', 'png');
            if(in_array($img_ex_to_lc, $allowed_exs)){
               $new_img_name = uniqid($uname, true).'.'.$img_ex_to_lc;
               $img_upload_path = '../upload/'.$new_img_name;
               move_uploaded_file($tmp_name, $img_upload_path);

               // Insert into Database
               $sql = "INSERT INTO users(fname, username, password,email,city, pp) 
                 VALUES(?,?,?,?,?,?)";
               $stmt = $conn->prepare($sql);
               $stmt->execute([$fname, $uname, $pass,$email,$city, $new_img_name]);

               header("Location: ../login.php?success=Your account has been created successfully");
                exit;
            }else {
               $em = "You can't upload files of this type";
               header("Location: ../register.php?error=$em&$data");
               exit;
            }
         }else {
            $em = "unknown error occurred!";
            header("Location: ../register.php?error=$em&$data");
            exit;
         }

        
      }else {
       	$sql = "INSERT INTO users(fname, username, password,email) 
       	        VALUES(?,?,?,?)";
       	$stmt = $conn->prepare($sql);
       	$stmt->execute([$fname, $uname, $pass,$email]);

       	header("Location: ../login.php?success=Your account has been created successfully");
   	    exit;
      }
    }


}else {
	header("Location: ../register.php?error=error");
	exit;
}
