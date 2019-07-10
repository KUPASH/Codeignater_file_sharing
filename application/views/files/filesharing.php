<form method="post" enctype="multipart/form-data">
    <input type="file" name="userFile">
    <button type="submit">Submit</button>
</form>
<a href="logout">Logout</a> </br>

<?  foreach ($files as $row) {
    echo '<br/><img src="/uploads/' . $row->hash_name[0] . '/' . $row->hash_name[1] . '/' . $row->hash_name . '" width="100"/> <br/>
    <a href="delete?del='.$row->id.'">Delete picture</a>
    <a href="download?name=' . $row->image_key . '">Link</a>
    <input value="localhost/files/download?name=' . $row->image_key . '"> <br/>';
    }
?>



