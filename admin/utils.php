<?php
$padding = 40;
$order = "ASC";
function treeView() {
    global $conn, $arrCategories;
    $query = "SELECT * FROM category";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $arrCategories[$row['id']] = array("parent_id" => $row["pid"], "name" => $row["name"]);
    }
}