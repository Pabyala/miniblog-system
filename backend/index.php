<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

$db_conn = mysqli_connect("localhost", "root", "", "react-miniblog");
if($db_conn === false){
    die("ERROR: Could Not Connect");
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    
    case "GET":
        $path = explode('/', $_SERVER['REQUEST_URI']);

        if(isset($path[4]) && is_numeric($path[4])){
            $json_array = array();
            $miniBlogId = $path[4];

            $getMiniBlogRow = mysqli_query($db_conn, "SELECT * FROM miniblog_list WHERE blog_id='$miniBlogId'");
            while($miniBlogRow = mysqli_fetch_array($getMiniBlogRow)){
                $json_array['miniBlogData'] = array('id'=>$miniBlogRow['blog_id'], "title"=>$miniBlogRow['blog_title'], "content"=>$miniBlogRow['blog_content']);
            }
            echo json_encode($json_array['miniBlogData']);
            return;
        } else {
            $allMiniBlog = mysqli_query($db_conn, "SELECT * FROM miniblog_list");
            //chaeck if the data base has data
            if(mysqli_num_rows($allMiniBlog) > 0){
                while($row = mysqli_fetch_array($allMiniBlog)){
                    $json_array["miniBlogData"][] = array("id"=>$row['blog_id'], "title"=>$row['blog_title'], "content"=>$row['blog_content']);
                }
                echo json_encode($json_array["miniBlogData"]);
                return;
            } else {
                echo json_encode(["result"=>"Please check the Data"]);
            }
        }
    break;

    case "POST":
        $miniBlogPostData = json_decode(file_get_contents("php://input"));

        //set the value from of form form the user
        $title = $miniBlogPostData->title;
        $content = $miniBlogPostData->content;

        //put the value from the user to the database 
        $result = mysqli_query($db_conn, "INSERT INTO miniblog_list (blog_title, blog_content) 
            VALUES('$title', '$content')");

        if($result){
            echo json_encode(["success" => "Blog added successfully"]);
            return;
        } else {
            echo json_encode(["result"=>"Please check the data"]);
            return;
        }
    break;

    case "PUT";
        $miniBlogUpdateData = json_decode(file_get_contents("php://input"));

        $blogId = $miniBlogUpdateData->id;
        $title = $miniBlogUpdateData->title;
        $content = $miniBlogUpdateData->content;

        $updateData = mysqli_query($db_conn, "UPDATE miniblog_list SET blog_title='$title', blog_content='$content' WHERE blog_id='$blogId'");
        if($updateData){
            echo json_encode(["success" => "Blog updated successfully"]);
            return;
        } else {
            echo json_encode(["result"=>"Please check the data"]);
            return;
        }

        print_r($miniBlogUpdateData);
    break;

    case "DELETE":
        $path = explode('/', $_SERVER["REQUEST_URI"]);

        $result = mysqli_query($db_conn, "DELETE FROM miniblog_list WHERE blog_id='$path[4]'");

        if($result){
            echo json_encode(["success" => "Blog deleted successfully"]);
            return;
        } else {
            echo json_encode(["result"=>"Please check the data"]);
            return;
        }
    break;
}

?>

