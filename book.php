<?php
header("Content-Type: application/json");
header("Acess-Control-Allow-Origin: *");
header("Acess-Control-Allow-Methods: POST,GET");
header("Acess-Control-Allow-Headers,Content-Type,Authorization");

$data = json_decode(file_get_contents("php://input") , true);

$response = [];
require_once "include/dbconnection.php";
$error = false;

if (isset($data) && !empty($data["type"]))
{

    $type = $data["type"];
    // Insert Book
    if ($type == 'insert')
    {
        if (isset($data["author"]))
        {
            $author = $data["author"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "author field is required"
            );
        }

        if (isset($data["title"]))
        {
            $title = $data["title"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "title field is required"
               
            );
        }

        if (isset($data["isbn"]))
        {
            $isbn = $data["isbn"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "isbn field is required"
               
            );
        }

        if (isset($data["release_date"]))
        {

            $release_date = $data["release_date"];
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $release_date))
            {
                $error = true;
                $response = array(
                    "message" => "release_date formate is yyyy-mm-dd"
                    
                );
            }
        }
        else
        {
            $release_date = date('Y-m-d');
        }
        if (!$error)
        {
            $query = "INSERT INTO books(author, title,isbn,release_date) 
											   VALUES ('" . $author . "', '" . $title . "','" . $isbn . "', '" . $release_date . "')";

            if (mysqli_query($conn, $query) or die("Insert Query Failed"))
            {
                $response = array(
                    "message" => "Book Inserted Successfully",
                    "status" => true,
                    "error" => false
                );

            }
            else
            {
                $response = array(
                    "message" => "Failed Book Not Inserted ",
                    "status" => false,
                    "error" => true
                );

            }
            echo json_encode($response);
        }
        else
        {
            echo json_encode(array_merge($response,array( "status" => false,
                    "error" => true)));
        }

        // Get Book
        
    }
    else if ($type == 'show')
    {

        if (isset($data["book_id"]))
        {
            $book_id = $data["book_id"];
            $query = "SELECT * FROM books where id='" . $book_id . "'";
        }
        else
        {
            $query = "SELECT * FROM books";
        }

        $result = mysqli_query($conn, $query) or die("Select Query Failed.");

        $count = mysqli_num_rows($result);

        if ($count > 0)
        {
            $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $response = array(
                "message" => "Book get Successfully",
                "status" => true,
                "error" => false,
                "data" => $row
            );

        }
        else
        {
            $response = array(
                "message" => "No Book Found.",
                "status" => false,
                "error" => false,
                "data" => []
            );

        }

        echo json_encode($response);

        // UPDATE Book
        
    }
    else if ($type == 'update')
    {

        if (isset($data["author"]))
        {
            $author = $data["author"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "author field is required",
                "status" => false,
                "error" => true
            );
        }
        if (isset($data["book_id"]))
        {
            $book_id = $data["book_id"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "book_id field is required"
            );
        }

        if (isset($data["title"]))
        {
            $title = $data["title"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "title field is required"
            );
        }

        if (isset($data["isbn"]))
        {
            $isbn = $data["isbn"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "isbn field is required"
            );
        }

        if (isset($data["release_date"]))
        {

            $release_date = $data["release_date"];
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $release_date))
            {
                $error = true;
                $response = array(
                    "message" => "release_date formate is yyyy-mm-dd"
                );
            }
        }
        if (!$error)
        {
            if (isset($release_date))
            {
                $query = "UPDATE books SET  author='" . $author . "', title='" . $title . "',isbn='" . $isbn . "',release_date='" . $release_date . "' where id='" . $book_id . "'";

            }
            else
            {
                $query = "UPDATE books SET  author='" . $author . "', title='" . $title . "',isbn='" . $isbn . "' where id='" . $book_id . "'";
            }

            if (mysqli_query($conn, $query) or die("Update Query Failed"))
            {
                $response = array(
                    "message" => "Book Update Successfully",
                    "status" => true,
                    "error" => false
                );

            }
            else
            {
                $response = array(
                    "message" => "Failed Book Not Update ",
                    "status" => false,
                    "error" => true
                );

            }
            echo json_encode($response);
        }
        else
        {
            echo json_encode(array_merge($response,array( "status" => false,
                    "error" => true)));
        }

    }
    else if ($type == 'delete')
    {
        if (isset($data["book_id"]))
        {
            $book_id = $data["book_id"];
        }
        else
        {
            $error = true;
            $response = array(
                "message" => "book_id field is required",
                "status" => false,
                "error" => true
            );
        }

        if (!$error)
        {
            $query = "DELETE FROM books where id='" . $book_id . "'";
            $result = mysqli_query($conn, $query) or die("Delete Query Failed.");

            if ($result)
            {
                $response = array(
                    "message" => "Book delete Successfully",
                    "status" => true,
                    "error" => false
                );

            }
            else
            {
                $response = array(
                    "message" => "Book not deleted .",
                    "status" => false,
                    "error" => false
                );

            }

            echo json_encode($response);
        }
        else
        {
            echo json_encode($response);
        }

    }
    else
    {
        echo json_encode(array(
            "message" => "type is not matched like show,insert,update,delete",
            "status" => false,
            "error" => true
        ));
    }

}
else
{
    echo json_encode(array(
        "message" => "type is required and please send data in json format.",
        "status" => false,
        "error" => true
    ));
}
?>
